<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LogoBriefController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\PromoLeadController;
use App\Http\Controllers\Api\WebsiteBriefController;
use App\Http\Controllers\Api\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/logo-brief', [LogoBriefController::class, 'store']);
Route::post('/website-briefs', [WebsiteBriefController::class, 'store']);
Route::post('/contacts', [ContactController::class, 'store']);
Route::post('/contact/autosave', [ContactController::class, 'autosave']);

Route::prefix('v1')->group(function () {
    Route::get('/packages', [PackageController::class, 'index']);
    Route::get('/packages/all-services', [PackageController::class, 'getAllServices']);
    Route::get('/packages/{id}', [PackageController::class, 'show']);

    // Blog routes — IMPORTANT: latest & featured must come before {slug}
    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/latest', [BlogController::class, 'latest']);
    Route::get('/blogs/featured', [BlogController::class, 'featured']);
    Route::get('/blogs/{slug}', [BlogController::class, 'show']);

    Route::get('/portfolios', [PortfolioController::class, 'apiIndex']);
    Route::get('/portfolio-categories', [PortfolioController::class, 'apiCategories']);
    
    
});

// ── Payment requests ────────────────────────────────────────────────────────

Route::post('payment-requests', [PaymentController::class, 'store']);
Route::get('payment-requests/by-link/{token}', [PaymentController::class, 'showByLink']);

Route::post('payment-requests/{id}/stripe/intent',  [PaymentController::class, 'stripeIntent']);
Route::post('payment-requests/{id}/stripe/confirm', [PaymentController::class, 'stripeConfirm']);

Route::post('payment-requests/{id}/cashapp/intent',  [PaymentController::class, 'cashappIntent']);
Route::post('payment-requests/{id}/cashapp/confirm', [PaymentController::class, 'cashappConfirm']);

Route::post('payment-requests/{id}/paypal/create-order', [PaymentController::class, 'paypalCreateOrder']);
Route::post('payment-requests/{id}/paypal/capture',      [PaymentController::class, 'paypalCapture']);

Route::post('payment-requests/{id}/zelle/approve', [PaymentController::class, 'zelleApprove']);

Route::post('stripe/webhook', [PaymentController::class, 'stripeWebhook']);

Route::post('/home-promo-lead', [PromoLeadController::class, 'store']);
Route::post('/home-promo-lead/autosave', [PromoLeadController::class, 'autosave']);
