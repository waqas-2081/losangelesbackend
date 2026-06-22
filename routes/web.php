<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\LogoBriefController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PortfolioController;
use App\Http\Controllers\Admin\PromoLeadController;
use App\Http\Controllers\Admin\WebsiteBriefController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Root Redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Guest Routes (not logged in) ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);

});

// ─── Authenticated Routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        // Website Briefs
        Route::get('/website-briefs', [WebsiteBriefController::class, 'index'])->name('website-briefs.index');
        Route::get('/website-briefs/{websiteBrief}', [WebsiteBriefController::class, 'show'])->name('website-briefs.show');
        Route::post('/website-briefs/{websiteBrief}/status', [WebsiteBriefController::class, 'updateStatus'])->name('website-briefs.status');
        Route::post('/website-briefs/{websiteBrief}/notes', [WebsiteBriefController::class, 'updateNotes'])->name('website-briefs.notes');
        Route::delete('/website-briefs/{websiteBrief}', [WebsiteBriefController::class, 'destroy'])->name('website-briefs.destroy');

        // Logo Briefs
        Route::get('/logo-briefs', [LogoBriefController::class, 'index'])->name('logo-briefs.index');
        Route::get('/logo-briefs/{logoBrief}', [LogoBriefController::class, 'show'])->name('logo-briefs.show');
        Route::post('/logo-briefs/{logoBrief}/status', [LogoBriefController::class, 'updateStatus'])->name('logo-briefs.status');
        Route::post('/logo-briefs/{logoBrief}/notes', [LogoBriefController::class, 'updateNotes'])->name('logo-briefs.notes');
        Route::delete('/logo-briefs/{logoBrief}', [LogoBriefController::class, 'destroy'])->name('logo-briefs.destroy');

        // Bulk destroy route MUST come before the {portfolio} wildcard route
        Route::delete('portfolios/bulk-destroy', [PortfolioController::class, 'bulkDestroy'])->name('portfolios.bulk-destroy');

        Route::get('/portfolios', [PortfolioController::class, 'index'])->name('portfolios.index');
        Route::get('/portfolios/create', [PortfolioController::class, 'create'])->name('portfolios.create');
        Route::post('/portfolios', [PortfolioController::class, 'store'])->name('portfolios.store');
        Route::get('/portfolios/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('portfolios.edit');
        Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
        Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');
        Route::post('portfolios/reorder', [PortfolioController::class, 'reorder'])->name('portfolios.reorder');

        Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
        Route::post('/contacts/{contact}/status', [ContactController::class, 'updateStatus'])->name('contacts.update-status');
        Route::post('/contacts/{contact}/notes', [ContactController::class, 'updateNotes'])->name('contacts.update-notes');
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

        Route::resource('packages', PackageController::class);
        Route::post('packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])
            ->name('packages.toggle-status');

        Route::resource('blogs', BlogController::class);
        Route::post('blogs/upload-image', [BlogController::class, 'uploadImage'])->name('blogs.upload-image');

        Route::resource('promo-leads', PromoLeadController::class)->only(['index', 'destroy']);

    });


});


