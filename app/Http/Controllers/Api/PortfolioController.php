<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function apiIndex(Request $request)
    {
        $query = Portfolio::where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $portfolios = $query->orderBy('sort_order')->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'category' => $item->category,
                    'image' => $item->image_url,
                    'sort_order' => $item->sort_order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $portfolios
        ]);
    }

    public function apiCategories()
    {
        return response()->json([
            'success' => true,
            'data' => Portfolio::categories()
        ]);
    }
}
