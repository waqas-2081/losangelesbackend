<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Portfolio::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status == '1');
        }

        $portfolios = $query->orderBy('sort_order')->paginate(15);
        $categories = Portfolio::categories();

        return view('admin.portfolios.index', compact('portfolios', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Portfolio::categories();
        return view('admin.portfolios.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer'
        ]);

        $category = $request->category;
        $isActive = $request->has('is_active') ? 1 : 0;
        $baseSortOrder = $request->sort_order ?? 0;

        $uploadedCount = 0;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('portfolios', 'public');

                Portfolio::create([
                    'category' => $category,
                    'image' => $path,
                    'is_active' => $isActive,
                    'sort_order' => $baseSortOrder + $index
                ]);

                $uploadedCount++;
            }
        }

        return redirect()->route('admin.portfolios.index')
            ->with('success', $uploadedCount . ' portfolio items created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Portfolio $portfolio)
    {
        $categories = Portfolio::categories();
        return view('admin.portfolios.edit', compact('portfolio', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable',
            'sort_order' => 'nullable|integer'
        ]);

        $data = [
            'category' => $request->category,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'sort_order' => $request->sort_order ?? 0
        ];

        if ($request->hasFile('image')) {
            if ($portfolio->image) {
                Storage::disk('public')->delete($portfolio->image);
            }
            $data['image'] = $request->file('image')->store('portfolios', 'public');
        }

        $portfolio->update($data);

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Portfolio item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->image) {
            Storage::disk('public')->delete($portfolio->image);
        }

        $portfolio->delete();

        return redirect()->route('admin.portfolios.index')
            ->with('success', 'Portfolio item deleted successfully.');
    }

    /**
     * Reorder portfolio items.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:portfolios,id',
            'orders.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->orders as $item) {
            Portfolio::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Order updated successfully']);
    }

    /**
     * Bulk delete portfolio items.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:portfolios,id',
        ]);

        $portfolios = Portfolio::whereIn('id', $request->ids)->get();
        $deletedCount = 0;

        foreach ($portfolios as $portfolio) {
            if ($portfolio->image) {
                Storage::disk('public')->delete($portfolio->image);
            }
            $portfolio->delete();
            $deletedCount++;
        }

        return redirect()->route('admin.portfolios.index')
            ->with('success', $deletedCount . ' portfolio item(s) deleted successfully.');
    }
}