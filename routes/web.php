<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VolunteerController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GeocodingController;
use App\Http\Controllers\WilayahController;

// Public Home & Interactive Map
Route::get('/', [HomeController::class, 'index'])->name('home');

// API Endpoints
Route::get('/api/listings/nearby', [HomeController::class, 'nearbyListings'])->name('api.listings.nearby');
Route::get('/api/campaigns/nearby', [HomeController::class, 'nearbyCampaigns'])->name('api.campaigns.nearby');
Route::get('/api/regions/regencies', [HomeController::class, 'regencies'])->name('api.regions.regencies');
Route::get('/api/regions/regencies/{id}/districts', [HomeController::class, 'districtsByRegency'])->name('api.regions.districts');

// Geocoding & Autocomplete Search (Google Maps style)
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/api/geocoding/search', [GeocodingController::class, 'search'])->name('api.geocoding.search');
    Route::get('/api/geocoding/reverse', [GeocodingController::class, 'reverse'])->name('api.geocoding.reverse');
});

// EMSIFA Wilayah Indonesia API Proxy & Cache
Route::prefix('api/wilayah')->name('api.wilayah.')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces'])->name('provinces');
    Route::get('/regencies/{provinceId}', [WilayahController::class, 'regencies'])->name('regencies');
    Route::get('/districts/{regencyId}', [WilayahController::class, 'districts'])->name('districts');
});

// Public E-Commerce Food Catalog (SDD FR-45, FR-46, SDD 8.8)
Route::get('/catalog', [ListingController::class, 'catalog'])->name('catalog.index');
Route::get('/listings', [ListingController::class, 'catalog'])->name('listings.index');

// Public Listing Detail
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

// Public Campaigns
Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
Route::get('/campaigns/{slug}', [CampaignController::class, 'show'])->name('campaigns.show');

// Guest Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

// Authenticated Routes
Route::middleware(['auth', EnsureUserIsActive::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications-clear-read', [NotificationController::class, 'destroyAllRead'])->name('notifications.clear-read');

    // Listings
    Route::get('/my/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/my/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::post('/my/listings/{listing}/cancel', [ListingController::class, 'cancel'])->name('listings.cancel');
    Route::get('/my/listings/{listing}/duplicate', [ListingController::class, 'duplicate'])->name('listings.duplicate');

    // Claims
    Route::post('/listings/{listing}/claims', [ClaimController::class, 'store'])->name('claims.store');
    Route::post('/claims/quick-scan', [ClaimController::class, 'quickVerify'])->name('claims.quick-verify');
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
    Route::post('/reports', [ReportController::class, 'store'])->middleware('throttle:15,1')->name('reports.store');

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
        Route::post('/users/{user}/reset-penalties', [AdminController::class, 'resetUserPenalties'])->name('users.reset-penalties');

        Route::get('/listings', [AdminController::class, 'listings'])->name('listings');
        Route::post('/listings/{listing}/toggle-takedown', [AdminController::class, 'toggleListingTakedown'])->name('listings.toggle-takedown');
        Route::delete('/listings/{listing}', [AdminController::class, 'destroyListing'])->name('listings.destroy');

        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/reset-logo', [AdminController::class, 'resetLogo'])->name('settings.reset-logo');
        Route::post('/settings/reset-all', [AdminController::class, 'resetSettings'])->name('settings.reset-all');
    });
});
