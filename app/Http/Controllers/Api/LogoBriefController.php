<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLogoBriefRequest;
use App\Models\LogoBrief;
use App\Models\LogoBriefFile;
use App\Services\GmailApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LogoBriefController extends Controller
{
    private $gmailApiService;

    public function __construct()
    {
        try {
            if (class_exists(\App\Services\GmailApiService::class)) {
                $this->gmailApiService = new \App\Services\GmailApiService();
            }
        } catch (\Exception $e) {
            Log::error('Failed to initialize Gmail API service', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * POST /api/logo-brief
     * Public — called from React form.
     */
    public function store(StoreLogoBriefRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Remove files from data before creating model
        unset($data['reference_files']);

        $brief = LogoBrief::create($data);

        // ─── Handle file uploads ───────────────────────────────────────────────
        $uploadedFiles = [];
        if ($request->hasFile('reference_files')) {
            foreach ($request->file('reference_files') as $file) {
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path     = $file->storeAs("logo-briefs/{$brief->id}", $fileName, 'public');
                $size     = $file->getSize();
                $mimeType = $file->getMimeType();

                LogoBriefFile::create([
                    'logo_brief_id' => $brief->id,
                    'file_name'     => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'mime_type'     => $mimeType,
                    'file_size'     => $size,
                ]);

                // Human readable size
                if ($size >= 1048576) {
                    $humanSize = round($size / 1048576, 2) . ' MB';
                } elseif ($size >= 1024) {
                    $humanSize = round($size / 1024, 2) . ' KB';
                } else {
                    $humanSize = $size . ' B';
                }

                $uploadedFiles[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'url' => '/storage/' . $path,
                    'human_size' => $humanSize,
                    'mime_type' => $mimeType,
                ];
            }
        }

        Log::info('New logo brief submitted', ['id' => $brief->id, 'email' => $brief->email]);

        $this->sendBriefEmail($brief, $uploadedFiles);

        return response()->json([
            'success' => true,
            'message' => 'Logo brief submitted successfully.',
            'data'    => ['id' => $brief->id],
        ], 201);
    }

    /**
     * Send logo brief notification to admin via Gmail API.
     */
    private function sendBriefEmail(LogoBrief $brief, array $uploadedFiles = [])
    {
        try {
            if (!class_exists(GmailApiService::class)) {
                Log::warning('GmailApiService not available, skipping logo brief email', [
                    'brief_id' => $brief->id,
                ]);
                return;
            }

            if (!$this->gmailApiService) {
                $this->gmailApiService = new GmailApiService();
            }

            $adminEmail = env('ADMIN_EMAIL');
            $subject = 'New Logo Brief Form - ' . env('APP_NAME');

            // Build attachments section
            $attachmentsSection = '';
            if (!empty($uploadedFiles)) {
                $fileRows = '';
                foreach ($uploadedFiles as $file) {
                    $fullUrl = rtrim(env('APP_URL'), '/') . $file['url'];
                    $isImage = str_starts_with($file['mime_type'], 'image/');
                    $icon = $isImage ? '&#128247;' : '&#128196;';

                    $fileRows .= '
                    <tr>
                        <td style="padding: 8px 0; border-bottom: 1px solid #f7f7f7;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="28" valign="middle" style="font-size: 18px;">' . $icon . '</td>
                                    <td valign="middle">
                                        <a href="' . $fullUrl . '" target="_blank"
                                           style="font-size: 14px; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; text-decoration: underline; font-weight: bold;"
                                        >' . htmlspecialchars($file['original_name']) . '</a>
                                        <span style="font-size: 12px; color: #999; font-family: Arial, Helvetica, sans-serif; margin-left: 8px;">(' . $file['human_size'] . ')</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';
                }

                $attachmentsSection = '
                <tr>
                    <td style="padding: 24px 40px 0 40px;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="background-color: #f9f9f9; border-radius: 6px; padding: 16px 20px;">
                            <tr>
                                <td style="padding-bottom: 10px;">
                                    <span style="font-size: 14px; font-weight: bold; color: #1a1a2e; font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">
                                        Attached Files (' . count($uploadedFiles) . ')
                                    </span>
                                </td>
                            </tr>
                            ' . $fileRows . '
                        </table>
                    </td>
                </tr>';
            }

            $emailContent = '
            <tr>
                <td style="padding: 32px 40px 24px 40px;">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                                    <!-- Section: Contact -->
                                    ' . $this->sectionHeader('Contact Info') . '
                                    ' . $this->fieldRow('Name', $brief->name) . '
                                    ' . $this->fieldRow('Email', $brief->email) . '
                                    ' . ($brief->personal_phone ? $this->fieldRow('Personal Phone', $brief->personal_phone) : '') . '
                                    ' . ($brief->company_phone ? $this->fieldRow('Company Phone', $brief->company_phone) : '') . '

                                    <!-- Section: Logo Details -->
                                    ' . $this->sectionHeader('Logo Details') . '
                                    ' . ($brief->logo_name ? $this->fieldRow('Logo Name', $brief->logo_name) : '') . '
                                    ' . ($brief->company_slogan ? $this->fieldRow('Company Slogan', $brief->company_slogan) : '') . '
                                    ' . ($brief->industry ? $this->fieldRow('Industry', $brief->industry) : '') . '
                                    ' . ($brief->business_desc ? $this->fieldRow('Business Description', $brief->business_desc, true) : '') . '
                                    ' . ($brief->logo_description ? $this->fieldRow('Logo Description', $brief->logo_description, true) : '') . '

                                    <!-- Section: Competitor References -->
                                    ' . $this->sectionHeader('Competitor References') . '
                                    ' . ($brief->competitors_ref ? $this->fieldRow('Reference 1', $brief->competitors_ref, true) : '') . '
                                    ' . ($brief->competitors_ref_two ? $this->fieldRow('Reference 2', $brief->competitors_ref_two, true) : '') . '
                                    ' . ($brief->competitors_ref_three ? $this->fieldRow('Reference 3', $brief->competitors_ref_three, true) : '') . '

                                    <!-- Section: Design Preferences -->
                                    ' . $this->sectionHeader('Design Preferences') . '
                                    ' . ($brief->logo_type ? $this->fieldRow('Logo Type', $brief->logo_type) : '') . '
                                    ' . ($brief->logo_fonts ? $this->fieldRow('Logo Fonts', $brief->logo_fonts) : '') . '
                                    ' . ($brief->logo_color ? $this->fieldRow('Logo Color', $brief->logo_color) : '') . '
                                    ' . ($brief->primary_color ? $this->fieldRow('Primary Color', $brief->primary_color) : '') . '
                                    ' . ($brief->secondary_color ? $this->fieldRow('Secondary Color', $brief->secondary_color) : '') . '

                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            ' . $attachmentsSection;

            $htmlBody = $this->buildEmailTemplate($subject, $emailContent);

            $this->gmailApiService->sendEmail(
                $adminEmail,
                $subject,
                $htmlBody,
                env('APP_NAME')
            );

            Log::info('LogoBrief notification email sent', ['brief_id' => $brief->id, 'admin' => $adminEmail]);

        } catch (\Throwable $e) {
            Log::error('Failed to send LogoBrief notification email', [
                'brief_id' => $brief->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Render a gold section header row.
     */
    private function sectionHeader(string $title): string
    {
        return '
        <tr>
            <td style="padding: 20px 0 6px 0;">
                <span style="font-size: 11px; font-weight: bold; color: #f7a800; font-family: Arial, Helvetica, sans-serif; text-transform: uppercase; letter-spacing: 1px;">'
            . htmlspecialchars($title) .
            '</span>
            </td>
        </tr>';
    }

    /**
     * Render a single field row.
     */
    private function fieldRow(string $label, $value, bool $multiline = false): string
    {
        $displayValue = $multiline
            ? nl2br(htmlspecialchars((string) $value))
            : htmlspecialchars((string) $value);

        return '
        <tr>
            <td style="padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="font-size: 13px; color: #888; font-family: Arial, Helvetica, sans-serif; display: block; margin-bottom: 2px;">'
            . htmlspecialchars($label) .
            '</span>
                <span style="font-size: 15px; color: #222; font-family: Arial, Helvetica, sans-serif; font-weight: ' . ($multiline ? 'normal' : 'bold') . ';">'
            . $displayValue .
            '</span>
            </td>
        </tr>';
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
}