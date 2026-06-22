<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogoBrief;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LogoBriefController extends Controller
{
    /**
     * GET /admin/logo-briefs
     */
    public function index(Request $request): View
    {
        $query = LogoBrief::latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('logo_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $briefs = $query->paginate(15)->withQueryString();

        return view('admin.logo-briefs.index', compact('briefs'));
    }

    /**
     * GET /admin/logo-briefs/{logoBrief}
     */
    public function show(LogoBrief $logoBrief): View
    {
        $logoBrief->load('files');
        return view('admin.logo-briefs.show', compact('logoBrief'));
    }

    /**
     * POST /admin/logo-briefs/{logoBrief}/status
     */
    public function updateStatus(Request $request, LogoBrief $logoBrief): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
        ]);

        $logoBrief->update(['status' => $request->status]);

        return response()->json([
            'success'     => true,
            'status_text' => "Status updated to {$logoBrief->status_label}",
        ]);
    }

    /**
     * POST /admin/logo-briefs/{logoBrief}/notes
     */
    public function updateNotes(Request $request, LogoBrief $logoBrief): JsonResponse
    {
        $request->validate(['admin_notes' => 'nullable|string']);
        $logoBrief->update(['admin_notes' => $request->admin_notes]);

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /admin/logo-briefs/{logoBrief}
     */
    public function destroy(LogoBrief $logoBrief): RedirectResponse
    {
        foreach ($logoBrief->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }
        
        $logoBrief->delete();

        return redirect()->route('admin.logo-briefs.index')
                         ->with('success', 'Logo brief deleted successfully.');
    }
}   