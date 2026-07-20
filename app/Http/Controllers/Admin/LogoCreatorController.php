<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogoCreator;
use Illuminate\Http\Request;

class LogoCreatorController extends Controller
{
    /**
     * GET /admin/logo-creator
     */
    public function index(Request $request)
    {
        $query = LogoCreator::latest();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $briefs = $query->paginate(15)->withQueryString();

        return view('admin.logo-creator.index', compact('briefs'));
    }

    /**
     * GET /admin/logo-creator/{logoCreator}
     */
    public function show(LogoCreator $logoCreator)
    {
        return view('admin.logo-creator.show', ['brief' => $logoCreator]);
    }

    /**
     * POST /admin/logo-creator/{logoCreator}/status
     */
    public function updateStatus(Request $request, LogoCreator $logoCreator)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
        ]);

        $logoCreator->update(['status' => $request->status]);

        $labels = [
            'pending'     => 'Pending',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'rejected'    => 'Rejected',
        ];

        return response()->json([
            'success'     => true,
            'status_text' => $labels[$request->status],
        ]);
    }

    /**
     * POST /admin/logo-creator/{logoCreator}/notes
     */
    public function updateNotes(Request $request, LogoCreator $logoCreator)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);

        $logoCreator->update(['admin_notes' => $request->admin_notes]);

        return response()->json(['success' => true]);
    }

    /**
     * DELETE /admin/logo-creator/{logoCreator}
     */
    public function destroy(LogoCreator $logoCreator)
    {
        $logoCreator->delete();

        return redirect()
            ->route('admin.logo-creator.index')
            ->with('success', 'Logo creator brief deleted successfully.');
    }
}