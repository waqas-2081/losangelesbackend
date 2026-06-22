<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    private array $services = [
        'logo-design-services',
        'website-design-development-services',
        'video-animation-services',
        'mobile-app-development-services',
        'social-media-marketing-services',
        'search-engine-optimization-services',
    ];

    public function index(Request $request)
    {
        $services = $this->services;

        $search = $request->get('search', '');
        $serviceType = $request->get('service_type', '');
        $status = $request->get('status', '');

        $query = Package::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('badge', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (!empty($serviceType) && in_array($serviceType, $services)) {
            $query->where('service_type', $serviceType);
        }

        if ($status !== '' && $status !== null) {
            $query->where('is_active', $status == '1');
        }

        $packages = $query->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.packages.index', compact('packages', 'services', 'serviceType', 'search', 'status'));
    }

    public function create()
    {
        $services = $this->services;
        return view('admin.packages.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:' . implode(',', $this->services),
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:one_time,project',
            'badge' => 'nullable|string|max:50',
            'features' => 'required|array',
            'features.*' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['service_type']);
        $validated['is_active'] = $request->has('is_active');

        Package::create($validated);

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit(Package $package)
    {
        $services = $this->services;
        return view('admin.packages.edit', compact('package', 'services'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'service_type' => 'required|in:' . implode(',', $this->services),
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:one_time,project',
            'badge' => 'nullable|string|max:50',
            'features' => 'required|array',
            'features.*' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['service_type']);
        $validated['is_active'] = $request->has('is_active');

        $package->update($validated);

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function toggleStatus(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $package->is_active
        ]);
    }
    
}