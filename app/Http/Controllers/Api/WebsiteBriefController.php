<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebsiteBrief;
use App\Models\WebsiteBriefFile;
use App\Services\GmailApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WebsiteBriefController extends Controller
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
     * POST /api/website-briefs
     * Called by the React form to submit a new brief.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            // Contact
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'business_name' => 'required|string|max:255',
            // Website type
            'website_type' => 'required|in:informative_without_payment,informative_with_payment_services,informative_with_payment_products,ecommerce,custom_web_app',
            // Conditional - informative without payment
            'products_count' => 'nullable|string|max:255',
            'services_count_no_payment' => 'nullable|string|max:255',
            'future_images_products' => 'nullable|string',
            // Conditional - informative with payment (services)
            'services_count_with_price' => 'nullable|string',
            'accept_online_payments' => 'nullable|boolean',
            'payment_medium' => 'nullable|string|max:255',
            'future_images_services' => 'nullable|string',
            // Brand & Audience
            'business_description' => 'required|string',
            'business_industry' => 'nullable|string|max:255',
            'target_audience' => 'nullable|string|max:255',
            'overall_feel' => 'nullable|array',
            'overall_feel.*' => 'string|in:corporate,fun,trendy,friendly,hi-tech,minimal,dark,light',
            'competitors_references' => 'nullable|string',
            // Site Structure
            'has_domain' => 'nullable|boolean',
            'pages_count' => 'nullable|integer|min:1|max:100',
            'pages_list' => 'nullable|string',
            'has_logo' => 'nullable|boolean',
            'wants_logo_revamp' => 'nullable|boolean',
            'needs_hosting' => 'nullable|boolean',
            'needs_responsive' => 'nullable|boolean',
            // Add-ons
            'addon_features' => 'nullable|array',
            'addon_features.*' => 'string',
            // Files
            'files' => 'nullable|array|max:5',
            'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,zip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        unset($data['files']);

        $brief = WebsiteBrief::create($this->mapBriefToDatabase($data));

        // Handle file uploads
        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $originalName = $file->getClientOriginalName();
                $path = $file->store('website-briefs/' . $brief->id, 'public');
                $size = $file->getSize();
                $mimeType = $file->getMimeType();

                WebsiteBriefFile::create([
                    'website_brief_id' => $brief->id,
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'mime_type' => $mimeType,
                    'file_size' => $size,
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
                    'original_name' => $originalName,
                    'url' => '/public/storage/' . $path,
                    'human_size' => $humanSize,
                    'mime_type' => $mimeType,
                ];
            }
        }

        $this->sendBriefEmail($brief, $uploadedFiles);

        return response()->json([
            'success' => true,
            'message' => 'Your website brief has been submitted successfully!',
            'brief_id' => $brief->id,
        ], 201);
    }

    public function getClientWebsiteProjects(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get website briefs where email matches
        $projects = WebsiteBrief::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($brief) {
                return [
                    'id' => $brief->id,
                    'title' => $brief->business_name ?? 'Website Project',
                    'package' => $this->getWebsiteTypeLabel($brief->website_type),
                    'status' => $this->mapBriefStatus($brief->status ?? 'pending'),
                    'date' => $brief->created_at->format('M d, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    private function getWebsiteTypeLabel(?string $type): string
    {
        return match ($type) {
            'informative_without_payment' => 'Informative Website',
            'informative_with_payment_services' => 'Informative + Payments (Services)',
            'informative_with_payment_products' => 'Informative + Payments (Products)',
            'ecommerce' => 'eCommerce Website',
            'custom_web_app' => 'Custom Web App',
            default => 'Website Project'
        };
    }

    private function mapBriefStatus(?string $status): string
    {
        return match ($status) {
            'submitted', 'pending' => 'pending',
            'in_progress', 'processing' => 'in_progress',
            'review' => 'review',
            'completed', 'done' => 'completed',
            default => 'pending'
        };
    }

    /**
     * Send website brief notification to admin via Gmail API.
     */
    private function sendBriefEmail(WebsiteBrief $brief, array $uploadedFiles = [])
    {
        try {
            if (!class_exists(GmailApiService::class)) {
                Log::warning('GmailApiService not available, skipping website brief email', [
                    'brief_id' => $brief->id,
                ]);
                return;
            }

            if (!$this->gmailApiService) {
                $this->gmailApiService = new GmailApiService();
            }

            $adminEmail = env('ADMIN_EMAIL');
            $subject = 'New Website Brief Form - ' . env('APP_NAME');

            // Format helpers
            $boolLabel = fn($val) => $val ? 'Yes' : 'No';
            $websiteTypeMap = [
                'informative_without_payment' => 'Informative (No Payment)',
                'informative_with_payment_services' => 'Informative with Payment (Services)',
                'informative_with_payment_products' => 'Informative with Payment (Products)',
                'ecommerce' => 'eCommerce',
                'custom_web_app' => 'Custom Web App',
            ];
            $websiteTypeLabel = $websiteTypeMap[$brief->website_type] ?? ucwords(str_replace('_', ' ', $brief->website_type));

            $overallFeel = !empty($brief->overall_feel) ? implode(', ', array_map('ucfirst', $brief->overall_feel)) : '—';
            $addonFeatures = !empty($brief->addon_features) ? implode(', ', $brief->addon_features) : '—';

            // Build conditional rows based on website_type
            $conditionalRows = '';
            if ($brief->website_type === 'informative_without_payment') {
                if ($brief->products_count) {
                    $conditionalRows .= $this->fieldRow('Products Count', $brief->products_count);
                }
                if ($brief->services_count_no_payment) {
                    $conditionalRows .= $this->fieldRow('Services Count', $brief->services_count_no_payment);
                }
                if ($brief->future_images_products) {
                    $conditionalRows .= $this->fieldRow('Future Images / Products', $brief->future_images_products, true);
                }
            } elseif (in_array($brief->website_type, ['informative_with_payment_services', 'informative_with_payment_products'])) {
                if ($brief->services_count_with_price) {
                    $conditionalRows .= $this->fieldRow('Services Count (with Price)', $brief->services_count_with_price);
                }
                if (!is_null($brief->accept_online_payments)) {
                    $conditionalRows .= $this->fieldRow('Accept Online Payments', $boolLabel($brief->accept_online_payments));
                }
                if ($brief->payment_medium) {
                    $conditionalRows .= $this->fieldRow('Payment Medium', $brief->payment_medium);
                }
                if ($brief->future_images_services) {
                    $conditionalRows .= $this->fieldRow('Future Images / Services', $brief->future_images_services, true);
                }
            }

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
                                    ' . $this->fieldRow('Business Name', $brief->business_name) . '

                                    <!-- Section: Website Type -->
                                    ' . $this->sectionHeader('Website Type') . '
                                    ' . $this->fieldRow('Type', $websiteTypeLabel) . '
                                    ' . $conditionalRows . '

                                    <!-- Section: Brand & Audience -->
                                    ' . $this->sectionHeader('Brand & Audience') . '
                                    ' . $this->fieldRow('Business Description', $brief->business_description, true) . '
                                    ' . ($brief->business_industry ? $this->fieldRow('Industry', $brief->business_industry) : '') . '
                                    ' . ($brief->target_audience ? $this->fieldRow('Target Audience', $brief->target_audience) : '') . '
                                    ' . $this->fieldRow('Overall Feel', $overallFeel) . '
                                    ' . ($brief->competitors_references ? $this->fieldRow('Competitor References', $brief->competitors_references, true) : '') . '

                                    <!-- Section: Site Structure -->
                                    ' . $this->sectionHeader('Site Structure') . '
                                    ' . (!is_null($brief->has_domain) ? $this->fieldRow('Has Domain', $boolLabel($brief->has_domain)) : '') . '
                                    ' . ($brief->pages_count ? $this->fieldRow('Pages Count', $brief->pages_count) : '') . '
                                    ' . ($brief->pages_list ? $this->fieldRow('Pages List', $brief->pages_list, true) : '') . '
                                    ' . (!is_null($brief->has_logo) ? $this->fieldRow('Has Logo', $boolLabel($brief->has_logo)) : '') . '
                                    ' . (!is_null($brief->wants_logo_revamp) ? $this->fieldRow('Wants Logo Revamp', $boolLabel($brief->wants_logo_revamp)) : '') . '
                                    ' . (!is_null($brief->needs_hosting) ? $this->fieldRow('Needs Hosting', $boolLabel($brief->needs_hosting)) : '') . '
                                    ' . (!is_null($brief->needs_responsive) ? $this->fieldRow('Needs Responsive', $boolLabel($brief->needs_responsive)) : '') . '

                                    <!-- Section: Add-ons -->
                                    ' . $this->sectionHeader('Add-on Features') . '
                                    ' . $this->fieldRow('Selected Add-ons', $addonFeatures) . '

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

            Log::info('WebsiteBrief notification email sent', ['brief_id' => $brief->id, 'admin' => $adminEmail]);

        } catch (\Throwable $e) {
            Log::error('Failed to send WebsiteBrief notification email', [
                'brief_id' => $brief->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Map API field names to database column names.
     */
    private function mapBriefToDatabase(array $data): array
    {
        $map = [
            'business_description' => 'business_desc',
            'business_industry' => 'industry',
            'overall_feel' => 'feel',
            'competitors_references' => 'competitors',
            'pages_count' => 'page_count',
            'pages_list' => 'page_names',
            'wants_logo_revamp' => 'revamp_logo',
            'needs_hosting' => 'need_hosting',
            'needs_responsive' => 'need_responsive',
            'products_count' => 'product_showcase_count',
            'services_count_no_payment' => 'service_showcase_count',
            'services_count_with_price' => 'services_prices',
        ];

        $mapped = [];
        foreach ($data as $key => $value) {
            $mapped[$map[$key] ?? $key] = $value;
        }

        return $mapped;
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
                            <img src="https://sanjoselogodesign.com/assets/images/logo/logo-white.png"
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