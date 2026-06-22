<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoLead;
use Illuminate\Http\Request;

class PromoLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = PromoLead::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_email', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        return view('admin.promo-leads.index', compact('leads'));
    }

    public function destroy(PromoLead $promoLead)
    {
        $promoLead->delete();

        return redirect()->route('admin.promo-leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
}