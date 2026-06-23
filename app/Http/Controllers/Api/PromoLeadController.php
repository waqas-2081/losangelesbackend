<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoLead;
use Illuminate\Http\Request;

class PromoLeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'   => 'required|string|max:255',
            'customer_email'  => 'required|email|max:255',
            'customer_phone'  => 'required|string|max:50',
            'project_details' => 'nullable|string|max:2000',
            'source'          => 'nullable|string|max:100',
        ]);

        PromoLead::create($validated);

        return response()->json(['success' => true], 201);
    }

    public function autosave(Request $request)
    {
        $allowedFields = ['customer_name', 'customer_email', 'customer_phone', 'project_details', 'source'];
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

        if (empty($finalData['source'])) {
            $finalData['source'] = 'home_promo_popup';
        }

        try {
            $lead = PromoLead::create($finalData);

            return response()->json([
                'success' => true,
                'message' => 'Draft saved.',
                'data' => $lead
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