<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\User;
use App\Services\GmailApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    private $gmailApiService;

    public function __construct()
    {
        try {
            $this->gmailApiService = new GmailApiService();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Gmail API service', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 1 — Create payment request (called from PaymentInfoPage)
    // POST /api/payment-requests
    // ══════════════════════════════════════════════════════════════════

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'profile'        => 'nullable|string|max:100',
            'customer_name'  => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:30',
            'package_name'   => 'nullable|string|max:255',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:stripe,paypal,cashapp,zelle,venmo',
            'payment_type'   => 'nullable|in:front,upsell',
        ]);

        do {
            $paymentLink = $this->generatePaymentLink();
        } while (PaymentRequest::where('payment_link', $paymentLink)->exists());

        $paymentRequest = PaymentRequest::create([
            'profile'        => $validated['profile'] ?? null,
            'customer_name'  => $validated['customer_name'],
            'email'          => $validated['email'] ?? null,
            'phone'          => $validated['phone'] ?? null,
            'package_name'   => $validated['package_name'] ?? null,
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_type'   => $validated['payment_type'] ?? null,
            'status'         => 'pending',
            'payment_link'   => $paymentLink,
        ]);

        $this->sendPaymentWebhook('payment.pending', $paymentRequest);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $paymentRequest->id,
                'payment_link' => $paymentLink,
            ],
        ], 201);
    }

    // ══════════════════════════════════════════════════════════════════
    // Fetch payment request by payment_link token (public)
    // GET /api/payment-requests/by-link/{token}
    // ══════════════════════════════════════════════════════════════════

    public function showByLink(string $token): JsonResponse
    {
        $paymentRequest = PaymentRequest::where('payment_link', $token)->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $paymentRequest->id,
                'payment_link'   => $paymentRequest->payment_link,
                'customer_name'  => $paymentRequest->customer_name,
                'email'          => $paymentRequest->email,
                'phone'          => $paymentRequest->phone,
                'package_name'   => $paymentRequest->package_name,
                'amount'         => $paymentRequest->amount,
                'payment_method' => $paymentRequest->payment_method,
                'status'         => $paymentRequest->status,
                'profile'        => $paymentRequest->profile,
                // payment_type intentionally omitted — admin-only field
            ],
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2A — Create Stripe PaymentIntent
    // POST /api/payment-requests/{id}/stripe/intent
    // ══════════════════════════════════════════════════════════════════

    public function stripeIntent(Request $request, int $id): JsonResponse
    {
        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            return response()->json(['success' => false, 'message' => 'Already paid.'], 409);
        }

        if ($paymentRequest->stripe_payment_intent_id && $paymentRequest->stripe_client_secret) {
            return response()->json([
                'success'       => true,
                'client_secret' => $paymentRequest->stripe_client_secret,
            ]);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount'               => $paymentRequest->amountInCents(),
                'currency'             => 'usd',
                'payment_method_types' => ['card'],
                'metadata'             => [
                    'payment_request_id' => $paymentRequest->id,
                    'customer_name'      => $paymentRequest->customer_name,
                    'email'              => $paymentRequest->email ?? '',
                ],
                'description' => "Payment for {$paymentRequest->customer_name}",
            ]);

            $paymentRequest->update([
                'stripe_payment_intent_id' => $intent->id,
                'stripe_client_secret'     => $intent->client_secret,
                'status'                   => 'pending',
            ]);

            return response()->json([
                'success'       => true,
                'client_secret' => $intent->client_secret,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2B — Confirm Stripe payment after frontend confirms
    // POST /api/payment-requests/{id}/stripe/confirm
    // ══════════════════════════════════════════════════════════════════

    public function stripeConfirm(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            $loginToken = $this->getOrCreateUserToken($paymentRequest);
            return response()->json(['success' => true, 'message' => 'Already paid.', 'login_token' => $loginToken]);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($intent->status === 'succeeded') {
                $duplicate = PaymentRequest::where('transaction_id', $intent->id)
                    ->where('id', '!=', $paymentRequest->id)
                    ->exists();

                if (!$duplicate) {
                    $paymentRequest->update([
                        'status'         => 'paid',
                        'transaction_id' => $intent->id,
                    ]);
                }

                $paymentRequest->refresh();
                $loginToken = $this->registerOrLoginUser($paymentRequest);
                $this->sendPaymentInvoiceEmail($paymentRequest);
                $this->sendPaymentWebhook('payment.created', $paymentRequest);

                return response()->json(['success' => true, 'login_token' => $loginToken]);
            }

            return response()->json([
                'success' => false,
                'message' => "Payment not completed. Status: {$intent->status}",
            ], 402);
        } catch (ApiErrorException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2C — Create CashApp PaymentIntent (via Stripe)
    // POST /api/payment-requests/{id}/cashapp/intent
    // ══════════════════════════════════════════════════════════════════

    public function cashappIntent(Request $request, int $id): JsonResponse
    {
        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            return response()->json(['success' => false, 'message' => 'Already paid.'], 409);
        }

        if ($paymentRequest->cashapp_payment_intent_id && $paymentRequest->stripe_client_secret) {
            return response()->json([
                'success'       => true,
                'client_secret' => $paymentRequest->stripe_client_secret,
            ]);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount'               => $paymentRequest->amountInCents(),
                'currency'             => 'usd',
                'payment_method_types' => ['cashapp'],
                'metadata'             => [
                    'payment_request_id' => $paymentRequest->id,
                    'customer_name'      => $paymentRequest->customer_name,
                ],
            ]);

            $paymentRequest->update([
                'cashapp_payment_intent_id' => $intent->id,
                'stripe_client_secret'      => $intent->client_secret,
                'status'                    => 'pending',
            ]);

            return response()->json([
                'success'       => true,
                'client_secret' => $intent->client_secret,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2D — Confirm CashApp payment
    // POST /api/payment-requests/{id}/cashapp/confirm
    // ══════════════════════════════════════════════════════════════════

    public function cashappConfirm(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            $loginToken = $this->getOrCreateUserToken($paymentRequest);
            return response()->json(['success' => true, 'login_token' => $loginToken]);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($intent->status === 'succeeded') {
                $duplicate = PaymentRequest::where('transaction_id', $intent->id)
                    ->where('id', '!=', $paymentRequest->id)
                    ->exists();

                if (!$duplicate) {
                    $paymentRequest->update([
                        'status'         => 'paid',
                        'transaction_id' => $intent->id,
                    ]);
                }

                $paymentRequest->refresh();
                $loginToken = $this->registerOrLoginUser($paymentRequest);
                $this->sendPaymentInvoiceEmail($paymentRequest);
                $this->sendPaymentWebhook('payment.created', $paymentRequest);

                return response()->json(['success' => true, 'login_token' => $loginToken]);
            }

            return response()->json([
                'success' => false,
                'message' => "Payment not completed. Status: {$intent->status}",
            ], 402);
        } catch (ApiErrorException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2E — Create PayPal / Venmo order
    // POST /api/payment-requests/{id}/paypal/create-order
    // (Venmo uses the same endpoint — PayPal SDK handles funding source)
    // ══════════════════════════════════════════════════════════════════

    public function paypalCreateOrder(Request $request, int $id): JsonResponse
    {
        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            return response()->json(['success' => false, 'message' => 'Already paid.'], 409);
        }

        try {
            $accessToken = $this->getPayPalAccessToken();

            $baseUrl = config('services.paypal.mode') === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $response = \Http::withToken($accessToken)->post("{$baseUrl}/v2/checkout/orders", [
                'intent'         => 'CAPTURE',
                'purchase_units' => [[
                    'amount'      => [
                        'currency_code' => 'USD',
                        'value'         => number_format($paymentRequest->amount, 2, '.', ''),
                    ],
                    'description' => "Payment for {$paymentRequest->customer_name}",
                ]],
            ]);

            if (!$response->successful()) {
                throw new \Exception('PayPal order creation failed: ' . $response->body());
            }

            $order   = $response->json();
            $orderId = $order['id'];

            $paymentRequest->update([
                'paypal_order_id' => $orderId,
                'status'          => 'processing',
            ]);

            return response()->json([
                'success'  => true,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2F — Capture PayPal / Venmo order after approval
    // POST /api/payment-requests/{id}/paypal/capture
    // (Venmo uses the same endpoint — capture flow is identical)
    // ══════════════════════════════════════════════════════════════════

    public function paypalCapture(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            $loginToken = $this->getOrCreateUserToken($paymentRequest);
            return response()->json(['success' => true, 'login_token' => $loginToken]);
        }

        try {
            $accessToken = $this->getPayPalAccessToken();

            $baseUrl = config('services.paypal.mode') === 'live'
                ? 'https://api-m.paypal.com'
                : 'https://api-m.sandbox.paypal.com';

            $orderId  = $request->order_id;
            $response = \Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->withBody('{}', 'application/json')
                ->post("{$baseUrl}/v2/checkout/orders/{$orderId}/capture");

            if (!$response->successful()) {
                throw new \Exception('PayPal capture failed: ' . $response->body());
            }

            $capture = $response->json();

            if (($capture['status'] ?? '') === 'COMPLETED') {
                $captureId = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? $orderId;

                $duplicate = PaymentRequest::where('transaction_id', $captureId)
                    ->where('id', '!=', $paymentRequest->id)
                    ->exists();

                if (!$duplicate) {
                    $paymentRequest->update([
                        'status'         => 'paid',
                        'transaction_id' => $captureId,
                    ]);
                }

                $paymentRequest->refresh();
                $loginToken = $this->registerOrLoginUser($paymentRequest);
                $this->sendPaymentInvoiceEmail($paymentRequest);
                $this->sendPaymentWebhook('payment.created', $paymentRequest);

                return response()->json(['success' => true, 'login_token' => $loginToken]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not completed.',
            ], 402);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // STEP 2G — Zelle: admin approves payment
    // POST /api/payment-requests/{id}/zelle/approve
    // ══════════════════════════════════════════════════════════════════

    public function zelleApprove(Request $request, int $id): JsonResponse
    {
        $paymentRequest = PaymentRequest::findOrFail($id);

        if ($paymentRequest->isPaid()) {
            return response()->json(['success' => true, 'message' => 'Already approved.']);
        }

        $paymentRequest->update([
            'status'         => 'paid',
            'transaction_id' => 'ZELLE-' . strtoupper(Str::random(10)),
        ]);

        $paymentRequest->refresh();
        $this->registerOrLoginUser($paymentRequest);
        $this->sendPaymentInvoiceEmail($paymentRequest);
        $this->sendPaymentWebhook('payment.created', $paymentRequest);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // Stripe Webhook — server-side confirmation fallback
    // POST /api/stripe/webhook
    // ══════════════════════════════════════════════════════════════════

    public function stripeWebhook(Request $request): JsonResponse
    {
        $payload       = $request->getContent();
        $sigHeader     = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $prId   = $intent->metadata->payment_request_id ?? null;

            if ($prId) {
                $pr = PaymentRequest::find($prId);
                if ($pr && !$pr->isPaid()) {
                    $duplicate = PaymentRequest::where('transaction_id', $intent->id)
                        ->where('id', '!=', $pr->id)
                        ->exists();

                    if (!$duplicate) {
                        $pr->update([
                            'status'         => 'paid',
                            'transaction_id' => $intent->id,
                        ]);
                    }

                    $pr->refresh();
                    $this->registerOrLoginUser($pr);
                    $this->sendPaymentInvoiceEmail($pr);
                    $this->sendPaymentWebhook('payment.created', $pr);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════════════════════════════
    // USER REGISTRATION — auto-create account on first paid payment
    // ══════════════════════════════════════════════════════════════════

    private function registerOrLoginUser(PaymentRequest $paymentRequest): ?string
    {
        if (!$paymentRequest->email) {
            return null;
        }

        try {
            $isNew    = false;
            $password = null;

            $user = User::where('email', $paymentRequest->email)->first();

            if (!$user) {
                $password = $this->generatePassword();

                $user = User::create([
                    'name'     => $paymentRequest->customer_name,
                    'email'    => $paymentRequest->email,
                    'password' => Hash::make($password),
                    'phone'    => $paymentRequest->phone,
                ]);

                $isNew = true;
            }

            $paymentRequest->update(['user_id' => $user->id]);

            // if ($isNew && $password) {
            //     $this->sendWelcomeEmail($user, $password, $paymentRequest);
            // }

            $token = $user->createToken('payment-autologin')->plainTextToken;

            return $token;
        } catch (\Exception $e) {
            Log::error('Failed to register/login user after payment', [
                'payment_request_id' => $paymentRequest->id,
                'error'              => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function getOrCreateUserToken(PaymentRequest $paymentRequest): ?string
    {
        if (!$paymentRequest->email) {
            return null;
        }

        $user = User::where('email', $paymentRequest->email)->first();

        if (!$user) {
            return $this->registerOrLoginUser($paymentRequest);
        }

        return $user->createToken('payment-autologin')->plainTextToken;
    }

    private function sendWelcomeEmail(User $user, string $password, PaymentRequest $paymentRequest): void
    {
        try {
            if (!$this->gmailApiService) {
                $this->gmailApiService = new GmailApiService();
            }

            $subject    = 'Welcome to ' . env('APP_NAME') . ' — Your Account Details';
            $dashUrl    = 'https://sanjoselogodesign.com/dashboard';
            $invoiceUrl = 'https://sanjoselogodesign.com/genrate/invoice-' . $paymentRequest->payment_link;

            $emailContent = '
            <!-- Gold banner -->
            <tr>
                <td style="background-color: #f7a800; padding: 16px 40px;">
                    <p style="margin: 0; font-size: 16px; font-weight: bold; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif;">
                        &#127881;&nbsp; Your account is ready!
                    </p>
                </td>
            </tr>

            <tr>
                <td style="padding: 32px 40px 24px 40px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <p style="font-size: 15px; color: #333; font-family: Arial, Helvetica, sans-serif; margin: 0 0 20px 0;">
                                    Hi <strong>' . htmlspecialchars($user->name) . '</strong>,<br><br>
                                    Thank you for your payment! We\'ve automatically created a client account for you so you can track your projects and payments anytime.
                                </p>

                                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                    <tr>
                                        <td style="padding: 6px 0;">
                                            <span style="font-size: 11px; font-weight: bold; color: #f7a800; font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; letter-spacing: 1px;">Your Login Details</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Email</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($user->email) . '</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Password</span>
                                            <span style="font-size: 18px; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; font-weight: bold; letter-spacing: 2px; background: #f9f9f9; padding: 6px 12px; border-radius: 4px; display: inline-block;">' . htmlspecialchars($password) . '</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Amount Paid</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">$' . number_format($paymentRequest->amount, 2) . '</span>
                                        </td>
                                    </tr>

                                </table>

                                <p style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; margin: 16px 0 0 0;">
                                    &#128274;&nbsp; Please change your password after your first login.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Buttons -->
            <tr>
                <td style="padding: 0 40px 16px 40px;">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="padding-right: 12px;">
                                <a href="' . $dashUrl . '"
                                   style="display: inline-block; background-color: #1a1a2e; color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; text-decoration: none; padding: 12px 28px; border-radius: 6px;">
                                    &#128101;&nbsp; Go to Dashboard
                                </a>
                            </td>
                            <td>
                                <a href="' . $invoiceUrl . '"
                                   style="display: inline-block; background-color: #f7a800; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: bold; text-decoration: none; padding: 12px 28px; border-radius: 6px;">
                                    &#128196;&nbsp; View Invoice
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td style="padding: 0 40px 32px 40px;">
                    <p style="font-size: 13px; color: #aaa; font-family: Arial, Helvetica, sans-serif; margin: 0;">
                        If you did not make this payment, please contact us immediately.
                    </p>
                </td>
            </tr>';

            $htmlBody = $this->buildEmailTemplate($subject, $emailContent);

            $this->gmailApiService->sendEmail(
                $user->email,
                $subject,
                $htmlBody,
                env('APP_NAME')
            );

            Log::info('Welcome email sent', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // EMAIL — Send invoice to admin + customer
    // ══════════════════════════════════════════════════════════════════

    private function sendPaymentInvoiceEmail(PaymentRequest $paymentRequest)
    {
        try {
            if (!$this->gmailApiService) {
                $this->gmailApiService = new GmailApiService();
            }

            $subject     = 'New Payment Invoice - ' . env('APP_NAME');
            $invoiceUrl  = 'https://losangeleslogodesigns.com/genrate/invoice-' . $paymentRequest->payment_link;
            $methodLabel = ucfirst($paymentRequest->payment_method);

            $emailContent = '
            <tr>
                <td style="padding: 32px 40px 24px 40px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Customer Name</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($paymentRequest->customer_name) . '</span>
                                        </td>
                                    </tr>
                                    ' . ($paymentRequest->email ? '
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Email Address</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($paymentRequest->email) . '</span>
                                        </td>
                                    </tr>' : '') . '
                                    ' . ($paymentRequest->phone ? '
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Phone</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($paymentRequest->phone) . '</span>
                                        </td>
                                    </tr>' : '') . '
                                    ' . ($paymentRequest->package_name ? '
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Package</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($paymentRequest->package_name) . '</span>
                                        </td>
                                    </tr>' : '') . '
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Payment Method</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . $methodLabel . '</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Amount Paid</span>
                                            <span style="font-size: 22px; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">$' . number_format($paymentRequest->amount, 2) . '</span>
                                        </td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Invoice Button -->
            <tr>
                <td align="center" style="padding: 0 40px 36px 40px;">
                    <a href="' . $invoiceUrl . '"
                       target="_blank"
                       style="display: inline-block; background-color: #f7a800; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; font-size: 15px; font-weight: bold; text-decoration: none; padding: 14px 36px; border-radius: 6px; letter-spacing: 0.5px;">
                        &#128196;&nbsp; View Invoice
                    </a>
                </td>
            </tr>';

            $htmlBody = $this->buildEmailTemplate($subject, $emailContent);

            $recipients = array_filter([
                env('ADMIN_EMAIL'),
                $paymentRequest->email ?: null,
            ]);

            $recipients = array_values(array_unique($recipients));

            if (count($recipients) === 1) {
                $this->gmailApiService->sendEmail(
                    $recipients[0],
                    $subject,
                    $htmlBody,
                    env('APP_NAME')
                );
            } else {
                $this->gmailApiService->sendEmailToMultiple(
                    $recipients,
                    $subject,
                    $htmlBody,
                    env('APP_NAME')
                );
            }

            Log::info('Payment invoice email sent', [
                'payment_request_id' => $paymentRequest->id,
                'recipients'         => $recipients,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment invoice email', [
                'payment_request_id' => $paymentRequest->id,
                'error'              => $e->getMessage(),
            ]);
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // EMAIL TEMPLATE
    // ══════════════════════════════════════════════════════════════════

    private function buildEmailTemplate(string $subject, string $bodyRows): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 30px 0;">
        <tr>
            <td align="center">
                <table width="620" cellpadding="0" cellspacing="0" border="0" style="max-width: 620px; width: 100%; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #1a1a2e; padding: 28px 40px;">
                            <img src="https://losangeleslogodesigns.com/assets/la-logo-designs-DOx3q257.png"
                                 alt="' . env('APP_NAME') . '"
                                 style="max-height: 60px; max-width: 220px; display: block;"
                            />
                        </td>
                    </tr>

                    <!-- Body rows injected here -->
                    ' . $bodyRows . '

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #1a1a2e; padding: 20px 40px;">
                            <p style="margin: 0; font-size: 13px; color: #aaa; font-family: Arial, Helvetica, sans-serif;">
                                &copy; ' . date('Y') . ' ' . env('APP_NAME') . '. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════

    private function getPayPalAccessToken(): string
    {
        $clientId = config('services.paypal.client_id');
        $secret   = config('services.paypal.secret');
        $mode     = config('services.paypal.mode', 'sandbox');

        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $response = \Http::withBasicAuth($clientId, $secret)
            ->asForm()
            ->post("{$baseUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

        if (!$response->successful()) {
            throw new \Exception('Could not authenticate with PayPal.');
        }

        return $response->json('access_token');
    }

    private function generatePaymentLink(): string
    {
        return (string) mt_rand(10000000, 99999999)
             . (string) mt_rand(100000, 999999);
    }

    private function generatePassword(): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghjkmnpqrstuvwxyz';
        $digits  = '23456789';
        $special = '@#$!';

        $password  = $upper[random_int(0, strlen($upper) - 1)];
        $password .= $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];

        return str_shuffle($password);
    }

    // ══════════════════════════════════════════════════════════════════
    // WEBHOOK — Sync payment events to MSB Admin
    // ══════════════════════════════════════════════════════════════════

    private function sendPaymentWebhook(string $event, PaymentRequest $paymentRequest): void
    {
        Log::info('Webhook method reached', [
            'event'       => $event,
            'id'          => $paymentRequest->id,
            'webhook_url' => config('services.msbadmin.webhook_url'),
            'has_secret'  => !empty(config('services.msbadmin.webhook_secret')),
        ]);

        try {
            $webhookUrl = config('services.msbadmin.webhook_url');
            $secret     = config('services.msbadmin.webhook_secret');

            if (empty($webhookUrl) || empty($secret)) return;

            Http::withOptions(['verify' => false])
                ->withHeaders([
                    'X-Webhook-Secret' => $secret,
                    'Accept'           => 'application/json',
                ])
                ->timeout(10)
                ->post($webhookUrl, [
                    'event'  => $event,
                    'source' => config('app.name'),
                    'data'   => [
                        'lead_id'          => (int) $paymentRequest->id,
                        'customer_name'    => $paymentRequest->customer_name,
                        'email'            => $paymentRequest->email,
                        'phone_no'         => $paymentRequest->phone,
                        'package_name'     => $paymentRequest->package_name,
                        'price'            => $paymentRequest->amount,
                        'payment_link'     => $paymentRequest->payment_link,
                        'payment_method'   => $paymentRequest->payment_method  ?? null,
                        'payment_type'     => $paymentRequest->payment_type    ?? null,
                        'reference'        => $paymentRequest->profile         ?? null,
                        'status'           => $paymentRequest->status,
                        'invoice_date'     => $paymentRequest->created_at      ?? null,
                        'payment_date'     => $paymentRequest->updated_at      ?? null,
                        'invoice_no'       => 'invoice-' . $paymentRequest->payment_link,
                        'transaction_id'   => $paymentRequest->transaction_id  ?? null,
                        'payment_amount'   => $paymentRequest->amount,
                        'payment_currency' => 'usd',
                        'source'           => config('app.name'),
                    ],
                ]);

            Log::info("Webhook [{$event}] sent for payment_request_id={$paymentRequest->id}");

        } catch (\Exception $e) {
            Log::error('Webhook failed: ' . $e->getMessage());
        }
    }
}