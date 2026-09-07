<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    private ?string $pixelId;
    private ?string $accessToken;

    public function __construct()
    {
        $this->pixelId = env('META_PIXEL_ID');
        $this->accessToken = env('META_CAPI_ACCESS_TOKEN');
    }

    /**
     * Send a Purchase event to Meta Conversions API.
     * (Used by the leads/payments-table controller — fixmywebs.com)
     *
     * @param object $lead    Row from leads table (needs id, email, phone_no, ip)
     * @param object $payment Row from payments table (needs payment_amount, payment_currency)
     */
    public function sendPurchaseEvent(
        $lead,
        $payment,
        string $eventSourceUrl,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        $this->send(
            id: $lead->id,
            email: $lead->email ?? null,
            phone: $lead->phone_no ?? null,
            ip: $ip ?: ($lead->ip ?? null),
            userAgent: $userAgent,
            eventSourceUrl: $eventSourceUrl,
            amount: (float) $payment->payment_amount,
            currency: $payment->payment_currency ?? 'USD',
            customerName: $lead->customer_name ?? null,
        );
    }

    /**
     * Send a Purchase event to Meta Conversions API.
     * (Used by controllers not backed by the leads/payments schema —
     * e.g. the PaymentRequest-based API controller.)
     */
    public function sendPurchaseEventRaw(
        int $id,
        ?string $email,
        ?string $phone,
        string $eventSourceUrl,
        float $amount,
        string $currency = 'USD',
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $customerName = null
    ): void {
        $this->send(
            id: $id,
            email: $email,
            phone: $phone,
            ip: $ip,
            userAgent: $userAgent,
            eventSourceUrl: $eventSourceUrl,
            amount: $amount,
            currency: $currency,
            customerName: $customerName,
        );
    }

    private function send(
        int $id,
        ?string $email,
        ?string $phone,
        ?string $ip,
        ?string $userAgent,
        string $eventSourceUrl,
        float $amount,
        string $currency,
        ?string $customerName
    ): void {
        if (empty($this->pixelId) || empty($this->accessToken)) {
            Log::info('Meta CAPI skipped: pixel ID or access token not configured');
            return;
        }

        try {
            $userData = [];

            if (!empty($email)) {
                $userData['em'] = [$this->hash($email)];
            }

            if (!empty($phone)) {
                $userData['ph'] = [$this->hash($this->normalizePhone($phone))];
            }

            if (!empty($customerName)) {
                $parts = preg_split('/\s+/', trim($customerName), 2);
                if (!empty($parts[0])) {
                    $userData['fn'] = [$this->hash($parts[0])];
                }
                if (!empty($parts[1])) {
                    $userData['ln'] = [$this->hash($parts[1])];
                }
            }

            if (!empty($ip)) {
                $userData['client_ip_address'] = $ip;
            }

            if (!empty($userAgent)) {
                $userData['client_user_agent'] = $userAgent;
            }

            // Same ID any client-side fbq() call should use, so Meta dedupes
            // instead of double-counting when both client and server fire.
            $eventId = 'purchase_' . $id;

            $payload = [
                'data' => [
                    [
                        'event_name' => 'Purchase',
                        'event_time' => now()->timestamp,
                        'event_id' => $eventId,
                        'event_source_url' => $eventSourceUrl,
                        'action_source' => 'website',
                        'user_data' => $userData,
                        'custom_data' => [
                            'value' => $amount,
                            'currency' => strtoupper($currency),
                        ],
                    ],
                ],
            ];

            $response = Http::withOptions(['verify' => false])
                ->timeout(10)
                ->post("https://graph.facebook.com/v19.0/{$this->pixelId}/events", array_merge(
                    $payload,
                    ['access_token' => $this->accessToken]
                ));

            if ($response->failed()) {
                Log::error('Meta CAPI request failed', [
                    'id' => $id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } else {
                Log::info("Meta CAPI Purchase event sent for id={$id}, event_id={$eventId}");
            }
        } catch (\Exception $e) {
            // Never let a Meta API failure break the payment flow
            Log::error('Meta CAPI exception: ' . $e->getMessage(), ['id' => $id]);
        }
    }

    private function hash(string $value): string
    {
        return hash('sha256', strtolower(trim($value)));
    }

    private function normalizePhone(string $phone): string
    {
        // Meta expects digits only, no symbols/spaces
        return preg_replace('/[^0-9]/', '', $phone);
    }
}