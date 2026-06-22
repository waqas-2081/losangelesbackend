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
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'project_description' => 'nullable|string|max:5000',
        ]);

        $validated['status'] = 'pending';

        Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received. We will get back to you shortly.'
        ], 201);
    }

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
                'data' => $contact
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Auto-save failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}