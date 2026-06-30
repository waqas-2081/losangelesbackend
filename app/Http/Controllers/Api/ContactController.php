<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\GmailApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'           => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone_number'        => 'nullable|string|max:50',
            'company_name'        => 'nullable|string|max:255',
            'project_description' => 'nullable|string|max:5000',
        ]);

        $validated['status'] = 'pending';

        $contact = Contact::create($validated);

        // Send admin notification email
        $this->sendContactEmail($contact);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received. We will get back to you shortly.'
        ], 201);
    }

    /**
     * Send contact form notification to admin via Gmail API.
     */
    private function sendContactEmail(Contact $contact)
    {
        try {
            if (!$this->gmailApiService) {
                $this->gmailApiService = new GmailApiService();
            }

            $adminEmail = env('ADMIN_EMAIL');
            $subject    = 'New Contact Form - ' . env('APP_NAME');

            $emailContent = '
            <tr>
                <td style="padding: 32px 40px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="padding-top: 24px;">
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Full Name</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($contact->full_name ?? '—') . '</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Email Address</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($contact->email ?? '—') . '</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Phone Number</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($contact->phone_number ?? '—') . '</span>
                                        </td>
                                    </tr>
                                    ' . ($contact->company_name ? '
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Company Name</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: bold;">' . htmlspecialchars($contact->company_name) . '</span>
                                        </td>
                                    </tr>' : '') . '
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">Description</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif;">' . nl2br(htmlspecialchars($contact->project_description ?? '—')) . '</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0;">
                                            <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">IP Address</span>
                                            <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif;">' . htmlspecialchars($request_ip = request()->ip() ?? '—') . '</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';

            $htmlBody = $this->buildEmailTemplate($subject, $emailContent);

            $this->gmailApiService->sendEmail(
                $adminEmail,
                $subject,
                $htmlBody,
                env('APP_NAME')
            );

            Log::info('Contact notification email sent', ['contact_id' => $contact->id, 'admin' => $adminEmail]);

        } catch (\Exception $e) {
            Log::error('Failed to send contact notification email', [
                'contact_id' => $contact->id,
                'error'      => $e->getMessage()
            ]);
        }
    }

    /**
     * Build the full HTML email with header logo and footer.
     */
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

    /**
     * Auto-save draft — partial data, no validation, no email.
     */
    public function autosave(Request $request)
    {
        $allowedFields = ['full_name', 'email', 'phone_number', 'company_name', 'project_description'];
        $data = $request->only($allowedFields);

        $filtered = [];
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '' && trim($value) !== '') {
                $filtered[$key] = trim($value);
            }
        }

        if (empty($filtered)) {
            return response()->json([
                'success' => false,
                'message' => 'No data provided.'
            ], 422);
        }

        $finalData = array_merge(array_fill_keys($allowedFields, null), $filtered);

        try {
            $contact = Contact::create($finalData);

            return response()->json([
                'success' => true,
                'message' => 'Draft saved.',
                'data'    => $contact
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auto-save failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}