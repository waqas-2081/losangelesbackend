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
}