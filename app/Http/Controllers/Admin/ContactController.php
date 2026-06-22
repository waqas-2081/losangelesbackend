<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of contacts
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('company_name', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->latest()->paginate(15);
        $statuses = Contact::getStatuses();

        return view('admin.contacts.index', compact('contacts', 'statuses'));
    }

    /**
     * Show single contact
     */
    public function show(Contact $contact)
    {
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Update contact status
     */
    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,archived'
        ]);

        $contact->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Update admin notes
     */
    public function updateNotes(Request $request, Contact $contact)
    {
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);

        $contact->update(['admin_notes' => $request->admin_notes]);

        return response()->json([
            'success' => true,
            'message' => 'Notes saved successfully'
        ]);
    }

    /**
     * Delete contact
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact deleted successfully');
    }
}