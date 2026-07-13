<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogoBrief;
use App\Models\PaymentRequest;
use App\Models\WebsiteBrief;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserDashboardController extends Controller
{
    // ── PROFILE ──────────────────────────────────────────────────────────

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'company' => $user->company,
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'   => 'nullable|string|max:30',
            'company' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'company' => $user->company,
            ],
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return response()->json(['success' => true, 'message' => 'Password updated.']);
    }

    // ── PAYMENTS ──────────────────────────────────────────────────────────

    public function payments(Request $request): JsonResponse
    {
        $payments = PaymentRequest::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        $payments->getCollection()->transform(fn (PaymentRequest $p) => [
            'id'             => $p->id,
            'package_name'   => $p->package_name,
            'amount'         => $p->amount,
            'status'         => $p->status,
            'payment_method' => $p->payment_method,
            'payment_link'   => $p->payment_link,
            'created_at'     => $p->created_at,
        ]);

        return response()->json(['success' => true, 'data' => $payments]);
    }

    // ── LOGO PROJECTS ─────────────────────────────────────────────────────

    public function logoProjects(Request $request): JsonResponse
    {
        $briefs = LogoBrief::where('email', $request->user()->email)
            ->latest()
            ->paginate(10);

        $briefs->getCollection()->transform(fn (LogoBrief $b) => $this->mapLogoProject($b));

        return response()->json(['success' => true, 'data' => $briefs]);
    }

    public function logoProjectShow(Request $request, LogoBrief $logoBrief): JsonResponse
    {
        if ($logoBrief->email !== $request->user()->email) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => array_merge($this->mapLogoProject($logoBrief), [
                'business_desc'    => $logoBrief->business_desc,
                'logo_description' => $logoBrief->logo_description,
                'industry'         => $logoBrief->industry,
                'admin_notes'      => $logoBrief->admin_notes,
            ]),
        ]);
    }

    private function mapLogoProject(LogoBrief $b): array
    {
        return [
            'id'           => 'LOG-' . str_pad($b->id, 4, '0', STR_PAD_LEFT),
            'raw_id'       => $b->id,
            'name'         => $b->logo_name ?: $b->company_slogan ?: ('Logo brief #' . $b->id),
            'package_name' => null,
            'status'       => $b->status,
            'updated_at'   => $b->updated_at,
        ];
    }

    // ── WEBSITE PROJECTS ──────────────────────────────────────────────────

    public function websiteProjects(Request $request): JsonResponse
    {
        $briefs = WebsiteBrief::where('email', $request->user()->email)
            ->latest()
            ->paginate(10);

        $briefs->getCollection()->transform(fn (WebsiteBrief $b) => $this->mapWebsiteProject($b));

        return response()->json(['success' => true, 'data' => $briefs]);
    }

    public function websiteProjectShow(Request $request, WebsiteBrief $websiteBrief): JsonResponse
    {
        if ($websiteBrief->email !== $request->user()->email) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => array_merge($this->mapWebsiteProject($websiteBrief), [
                'business_desc'  => $websiteBrief->business_desc,
                'industry'       => $websiteBrief->industry,
                'target_audience'=> $websiteBrief->target_audience,
                'admin_notes'    => $websiteBrief->admin_notes,
            ]),
        ]);
    }

    private function mapWebsiteProject(WebsiteBrief $b): array
    {
        return [
            'id'           => 'WEB-' . str_pad($b->id, 4, '0', STR_PAD_LEFT),
            'raw_id'       => $b->id,
            'name'         => $b->business_name ?: ('Website brief #' . $b->id),
            'type'         => $b->website_type,
            'package_name' => null,
            'status'       => $b->status,
            'updated_at'   => $b->updated_at,
        ];
    }
}