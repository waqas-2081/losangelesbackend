<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function index(Request $request): JsonResponse
    {
        $serviceType = $request->get('service_type', 'logo-design-services');

        $packages = Package::active()
            ->byServiceType($serviceType)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($package) => [
                'id' => $package->id,
                'name' => $package->name,
                'badge' => $package->badge,
                'price' => number_format($package->price, 2),
                'price_type' => $package->price_type === 'one_time' ? '/one time' : '/project',
                'features' => $package->features,
                'button_text' => 'Get Started',
                'button_url' => '/contact?package=' . $package->id,
            ]);

        return response()->json([
            'success' => true,
            'data' => $packages
        ]);
    }

    public function show($id): JsonResponse
    {
        $package = Package::active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $package->id,
                'name' => $package->name,
                'badge' => $package->badge,
                'price' => number_format($package->price, 2),
                'price_type' => $package->price_type === 'one_time' ? '/one time' : '/project',
                'features' => $package->features,
                'button_text' => 'Get Started',
                'button_url' => '/contact?package=' . $package->id,
            ]
        ]);
    }

    public function getAllServices(): JsonResponse
    {
        $result = [];

        foreach ($this->services as $service) {
            $packages = Package::active()
                ->byServiceType($service)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($package) => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'badge' => $package->badge,
                    'price' => number_format($package->price, 2),
                    'price_type' => $package->price_type === 'one_time' ? '/one time' : '/project',
                    'features' => $package->features,
                    'button_text' => 'Get Started'
                ]);

            if ($packages->isNotEmpty()) {
                $result[$service] = $packages;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}