<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteBrief;
use App\Models\WebsiteBriefFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebsiteBriefController extends Controller
{
    /**
     * GET /admin/website-briefs
     */
    public function index(Request $request): View
    {
        $query = WebsiteBrief::latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $briefs = $query->paginate(15)->withQueryString();

        return view('admin.website-briefs.index', compact('briefs'));
    }

    /**
     * GET /admin/website-briefs/{brief}
     */
    public function show(WebsiteBrief $websiteBrief): View
    {
        $websiteBrief->load('files');
        return view('admin.website-briefs.show', ['brief' => $websiteBrief]);
    }

    /**
     * POST /admin/website-briefs/{brief}/status
     */
    public function updateStatus(Request $request, WebsiteBrief $websiteBrief): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
        ]);

        $websiteBrief->update(['status' => $request->status]);

        return response()->json([
            'success'     => true,
            'status_text' => "Status updated to {$websiteBrief->status_label}",
        ]);
    }

    /**
     * POST /admin/website-briefs/{brief}/notes
     */
    public function updateNotes(Request $request, WebsiteBrief $websiteBrief): JsonResponse
    {
        $request->validate(['admin_notes' => 'nullable|string']);
        $websiteBrief->update(['admin_notes' => $request->admin_notes]);

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /admin/website-briefs/{brief}
     */
    public function destroy(WebsiteBrief $websiteBrief): RedirectResponse
    {
        // Delete stored files from disk
        foreach ($websiteBrief->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $websiteBrief->delete();

        return redirect()->route('admin.website-briefs.index')
                         ->with('success', 'Website brief deleted successfully.');
    }
}