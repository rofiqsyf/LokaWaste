<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VolunteerController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WilayahController;

// Public Home & Interactive Map
Route::get('/', [HomeController::class, 'index'])->name('home');

// API Endpoints
Route::get('/api/listings/nearby', [HomeController::class, 'nearbyListings'])->name('api.listings.nearby');
Route::get('/api/campaigns/nearby', [HomeController::class, 'nearbyCampaigns'])->name('api.campaigns.nearby');
Route::get('/api/regions/regencies', [HomeController::class, 'regencies'])->name('api.regions.regencies');
Route::get('/api/regions/regencies/{id}/districts', [HomeController::class, 'districtsByRegency'])->name('api.regions.districts');

// EMSIFA Wilayah Indonesia API Proxy & Cache
Route::prefix('api/wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces'])->name('provinces');
    Route::get('/regencies/{provinceId}', [WilayahController::class, 'regencies'])->name('regencies');
    Route::get('/districts/{regencyId}', [WilayahController::class, 'districts'])->name('districts');
});

// Public Listing Detail
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

// Public Campaigns
Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');

// Guest Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Listings
    Route::get('/my/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/my/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::post('/my/listings/{listing}/cancel', [ListingController::class, 'cancel'])->name('listings.cancel');
    Route::get('/my/listings/{listing}/duplicate', [ListingController::class, 'duplicate'])->name('listings.duplicate');

    // Claims
    Route::post('/listings/{listing}/claims', [ClaimController::class, 'store'])->name('claims.store');
    Route::post('/claims/{claim}/approve', [ClaimController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{claim}/reject', [ClaimController::class, 'reject'])->name('claims.reject');
    Route::post('/claims/{claim}/cancel', [ClaimController::class, 'cancel'])->name('claims.cancel');
    Route::post('/claims/{claim}/pickup', [ClaimController::class, 'verifyPickup'])->name('claims.verify-pickup');
    Route::get('/claims/{claim}/pickup-details', [ClaimController::class, 'pickupDetails'])->name('claims.pickup-details');
    Route::post('/claims/{claim}/review', [ClaimController::class, 'review'])->name('claims.review');

    // Volunteer Application & Areas
    Route::get('/volunteer/apply', [VolunteerController::class, 'showApplyForm'])->name('volunteer.apply');
    Route::post('/volunteer/apply', [VolunteerController::class, 'apply'])->name('volunteer.apply.submit');
    Route::get('/volunteer/areas', [VolunteerController::class, 'myAreas'])->name('volunteer.areas');
    Route::post('/volunteer/areas/{area}/resign', [VolunteerController::class, 'resign'])->name('volunteer.areas.resign');

    // Volunteer Campaigns Management
    Route::get('/volunteer/campaigns', [CampaignController::class, 'volunteerIndex'])->name('volunteer.campaigns.index');
    Route::get('/volunteer/campaigns/create', [CampaignController::class, 'create'])->name('volunteer.campaigns.create');
    Route::post('/volunteer/campaigns', [CampaignController::class, 'store'])->name('volunteer.campaigns.store');
    Route::post('/volunteer/campaigns/{campaign}/submit', [CampaignController::class, 'submit'])->name('volunteer.campaigns.submit');
    Route::post('/volunteer/campaigns/{campaign}/updates', [CampaignController::class, 'postUpdate'])->name('volunteer.campaigns.updates');
    Route::post('/volunteer/campaigns/{campaign}/close', [CampaignController::class, 'close'])->name('volunteer.campaigns.close');

    // Reports
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // Admin Routes
    Route::prefix('admin')->middleware(EnsureAdmin::class)->as('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/volunteer-applications', [AdminController::class, 'volunteerApplications'])->name('volunteer-applications');
        Route::post('/volunteer-areas/{area}/decide', [AdminController::class, 'decideVolunteerArea'])->name('volunteer-areas.decide');
        Route::get('/campaigns', [AdminController::class, 'campaigns'])->name('campaigns');
        Route::post('/campaigns/{campaign}/decide', [AdminController::class, 'decideCampaign'])->name('campaigns.decide');
        Route::post('/campaigns/{campaign}/reassign', [AdminController::class, 'reassignCampaign'])->name('campaigns.reassign');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::post('/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/toggle-verify', [AdminController::class, 'toggleUserVerify'])->name('users.toggle-verify');
        Route::post('/users/{user}/toggle-active', [AdminController::class, 'toggleUserActive'])->name('users.toggle-active');
    });
});
