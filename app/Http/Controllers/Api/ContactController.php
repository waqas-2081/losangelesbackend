<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone_number'        => 'nullable|string|max:50',
            'company_name'        => 'nullable|string|max:255',
            'project_description' => 'nullable|string|max:5000',
        ]);

        $validated['status'] = 'pending';

        Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received. We will get back to you shortly.'
        ], 201);
    }
}