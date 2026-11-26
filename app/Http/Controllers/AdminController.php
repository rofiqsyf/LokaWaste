<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Models\Claim;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Models\Report;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerArea;
use App\Notifications\LokaNotification;
use App\Services\CampaignService;
use App\Services\ImageUploadService;
use App\Services\VolunteerApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function __construct(
        protected VolunteerApplicationService $volunteerService,
        protected CampaignService $campaignService
    ) {}

    public function index()
    {
        // Statistics
        $totalPortionsSaved = (int) Claim::where('status', 'picked_up')->sum('portions_approved');
        $totalListings = FoodListing::count();
        $totalVolunteers = User::whereHas('volunteerAreas', fn($q) => $q->where('status', 'approved'))->count();
        $totalUsers = User::count();

        // Review Queue (ordered by oldest pending first)
        $pendingApplications = VolunteerApplication::with(['user', 'regency', 'areas.district'])
            ->whereHas('areas', fn($q) => $q->where('status', 'pending'))
            ->oldest()
            ->get();

        $pendingCampaigns = FoodCampaign::with(['user', 'district.regency'])
            ->where('status', CampaignStatus::PENDING_REVIEW->value)
            ->oldest()
            ->get();

        $openReports = Report::with(['reporter', 'listing', 'campaign', 'reportedUser'])
            ->where('status', 'open')
            ->latest()
            ->get();

        return view('admin.index', compact(
            'totalPortionsSaved',
            'totalListings',
            'totalVolunteers',
            'totalUsers',
            'pendingApplications',
            'pendingCampaigns',
            'openReports'
        ));
    }

    public function volunteerApplications()
    {
        $applications = VolunteerApplication::with(['user', 'regency', 'areas.district', 'areas.decidedByUser'])
            ->latest()
            ->paginate(15);

        return view('admin.volunteer-applications', compact('applications'));
    }

    public function decideVolunteerArea(VolunteerArea $area, Request $request)
    {
        $action = $request->input('action'); // approve, reject, revoke
        $note = $request->input('decision_note');
        $admin = Auth::user();

        match ($action) {
            'approve' => $this->volunteerService->approveArea($area, $admin, $note),
            'reject' => $this->volunteerService->rejectArea($area, $admin, $note),
            'revoke' => $this->volunteerService->revokeArea($area, $admin, $note),
            default => abort(422, 'Aksi tidak valid.'),
        };

        $area->loadMissing(['user', 'district']);
        $statusText = match ($action) {
            'approve' => "Pengajuan relawan Anda untuk Kec. {$area->district->name} telah disetujui.",
            'reject' => "Pengajuan relawan Anda untuk Kec. {$area->district->name} ditolak." . ($note ? " Catatan: {$note}" : ''),
            'revoke' => "Izin relawan Anda di Kec. {$area->district->name} telah dicabut admin." . ($note ? " Catatan: {$note}" : ''),
        };

        $area->user?->notify(new LokaNotification(
            title: 'Status Relawan Diperbarui',
            message: $statusText,
            category: 'system',
            icon: $action === 'approve' ? 'check-circle' : 'bell',
            actionUrl: route('volunteer.areas'),
            actionLabel: 'Lihat Wilayah Saya'
        ));

        return back()->with('success', "Keputusan untuk Kecamatan {$area->district->name} berhasil disimpan.");
    }

    public function campaigns()
    {
        $campaigns = FoodCampaign::with(['user', 'district.regency', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('admin.campaigns', compact('campaigns'));
    }

    public function decideCampaign(FoodCampaign $campaign, Request $request)
    {
        $action = $request->input('action'); // publish, revision, reject, takedown
        $note = (string) $request->input('review_note', '');
        $admin = Auth::user();

        match ($action) {
            'publish' => $this->campaignService->approveAndPublish($campaign, $admin, $note),
            'revision' => $this->campaignService->requestRevision($campaign, $admin, $note),
            'reject' => $this->campaignService->reject($campaign, $admin, $note),
            'takedown' => $campaign->update(['status' => 'taken_down', 'review_note' => $note]),
            default => abort(422, 'Aksi tidak valid.'),
        };

        $campMsg = match ($action) {
            'publish' => "Galang makanan \"{$campaign->title}\" telah diterbitkan dan kini tampil di peta.",
            'revision' => "Admin meminta revisi untuk galang \"{$campaign->title}\"." . ($note ? " Catatan: {$note}" : ''),
            'reject' => "Galang makanan \"{$campaign->title}\" ditolak." . ($note ? " Alasan: {$note}" : ''),
            'takedown' => "Galang makanan \"{$campaign->title}\" diturunkan sementara oleh admin.",
        };

        $campaign->user?->notify(new LokaNotification(
            title: 'Moderasi Galang Pangan: ' . $campaign->title,
            message: $campMsg,
            category: 'system',
            icon: $action === 'publish' ? 'check-circle' : 'bell',
            actionUrl: route('volunteer.campaigns.index'),
            actionLabel: 'Kelola Galang Saya'
        ));

        return back()->with('success', "Status galang makanan berhasil diperbarui.");
    }

    public function reassignCampaign(FoodCampaign $campaign, Request $request)
    {
        $validated = $request->validate([
            'new_volunteer_id' => ['required', 'exists:users,id'],
        ]);

        $newVolunteer = User::findOrFail($validated['new_volunteer_id']);
        $admin = Auth::user();

        try {
            $this->campaignService->reassign($campaign, $newVolunteer, $admin);
            return back()->with('success', "Galang berhasil dialihkan ke {$newVolunteer->name}.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reports()
    {
        $reports = Report::with(['reporter', 'listing', 'campaign', 'reportedUser'])
            ->latest()
            ->paginate(20);

        return view('admin.reports', compact('reports'));
    }

    public function resolveReport(Report $report, Request $request)
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:open,reviewed,resolved,dismissed'],
        ]);

        $report->update([
            'status' => $validated['status'] ?? 'resolved',
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function users(Request $request)
    {
        $query = User::with(['district.regency', 'volunteerAreas.district']);

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%");
            });
        }

        $filter = $request->input('filter', 'all');
        if ($filter === 'verified') {
            $query->where('is_verified', true);
        } elseif ($filter === 'unverified') {
            $query->where('is_verified', false);
        } elseif ($filter === 'restricted') {
            $query->where(function ($q) {
                $q->where('no_show_count', '>', 0)
                  ->orWhere('claim_restricted_until', '>', now());
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => User::count(),
            'verified' => User::where('is_verified', true)->count(),
            'volunteers' => User::whereHas('volunteerAreas', fn($q) => $q->where('status', 'approved'))->count(),
            'restricted' => User::where('claim_restricted_until', '>', now())->orWhere('no_show_count', '>', 0)->count(),
        ];

        return view('admin.users', compact('users', 'stats', 'search', 'filter'));
    }

    public function toggleUserVerify(User $user)
    {
        $user->is_verified = !$user->is_verified;
        $user->save();

        $user->notify(new LokaNotification(
            title: $user->is_verified ? 'Akun Anda Terverifikasi' : 'Verifikasi Akun Dicabut',
            message: $user->is_verified
                ? 'Selamat! Akun/usaha pangan Anda telah terverifikasi oleh administrator.'
                : 'Status verifikasi akun Anda telah dicabut sementara oleh administrator.',
            category: 'system',
            icon: $user->is_verified ? 'check-circle' : 'bell',
            actionUrl: route('dashboard'),
            actionLabel: 'Buka Dashboard'
        ));

        return back()->with('success', "Status verifikasi {$user->name} diperbarui.");
    }

    public function toggleUserActive(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun administrator Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', "Status akun {$user->name} diperbarui.");
    }

    public function resetUserPenalties(User $user)
    {
        $user->no_show_count = 0;
        $user->claim_restricted_until = null;
        $user->save();

        $user->notify(new LokaNotification(
            title: 'Pembatasan Klaim Direset',
            message: 'Catatan no-show dan pembatasan klaim akun Anda telah dipulihkan oleh administrator.',
            category: 'system',
            icon: 'check-circle',
            actionUrl: route('catalog.index'),
            actionLabel: 'Jelajah Pangan'
        ));

        return back()->with('success', "Batasan no-show dan pembatasan klaim untuk {$user->name} berhasil direset.");
    }

    /**
     * Manage food surplus resources on the platform.
     */
    public function listings(Request $request)
    {
        $query = FoodListing::with(['user', 'category', 'district.regency', 'claims']);

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('pickup_address', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $status = $request->input('status', 'all');
        if ($status === 'available') {
            $query->where('status', 'available')->where('expires_at', '>', now());
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($status === 'expired') {
            $query->where(function ($q) {
                $q->where('status', 'expired')
                  ->orWhere(fn($sq) => $sq->where('status', 'available')->where('expires_at', '<=', now()));
            });
        } elseif ($status === 'taken_down') {
            $query->where('status', 'taken_down');
        }

        $listings = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => FoodListing::count(),
            'available' => FoodListing::where('status', 'available')->where('expires_at', '>', now())->count(),
            'portions_saved' => (int) Claim::where('status', 'picked_up')->sum('portions_approved'),
            'taken_down' => FoodListing::where('status', 'taken_down')->count(),
        ];

        return view('admin.listings', compact('listings', 'stats', 'search', 'status'));
    }

    /**
     * Takedown or restore a food listing.
     */
    public function toggleListingTakedown(FoodListing $listing, Request $request)
    {
        if ($listing->status === 'taken_down') {
            $newStatus = $listing->expires_at->isPast() ? 'expired' : ($listing->portions_left > 0 ? 'available' : 'completed');
            $listing->update(['status' => $newStatus]);

            $listing->user?->notify(new LokaNotification(
                title: 'Listing Dipulihkan: ' . $listing->title,
                message: "Listing pangan \"{$listing->title}\" telah dipulihkan oleh administrator dan kembali sesuai status aktifnya.",
                category: 'listing',
                icon: 'check-circle',
                actionUrl: route('listings.show', $listing->id),
                actionLabel: 'Lihat Listing'
            ));

            return back()->with('success', "Listing '{$listing->title}' berhasil dipulihkan.");
        } else {
            $listing->update(['status' => 'taken_down']);

            $listing->user?->notify(new LokaNotification(
                title: 'Listing Diturunkan Admin: ' . $listing->title,
                message: "Listing pangan \"{$listing->title}\" diturunkan sementara dari daftar publik oleh administrator untuk menjaga keamanan komunitas.",
                category: 'listing',
                icon: 'bell',
                actionUrl: route('dashboard', ['tab' => 'bagikan']),
                actionLabel: 'Lihat Dashboard'
            ));

            return back()->with('warning', "Listing '{$listing->title}' telah dinonaktifkan/diturunkan dari website.");
        }
    }

    /**
     * Delete inappropriate food listing.
     */
    public function destroyListing(FoodListing $listing)
    {
        $title = $listing->title;
        $listing->delete();

        return back()->with('success', "Listing '{$title}' berhasil dihapus permanen.");
    }

    /**
     * Display dynamic website configuration CMS for admin.
     */
    public function settings()
    {
        $settings = SiteSetting::allMap();
        $logoUrl = SiteSetting::logoUrl();
        $faviconUrl = SiteSetting::faviconUrl();

        return view('admin.settings', compact('settings', 'logoUrl', 'faviconUrl'));
    }

    /**
     * Save all website settings and branding assets dynamically.
     */
    public function updateSettings(Request $request, ImageUploadService $imageService)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:60'],
            'site_tagline' => ['nullable', 'string', 'max:80'],
            'site_description' => ['nullable', 'string', 'max:320'],
            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:1024'],

            'home_title' => ['required', 'string', 'max:120'],
            'home_sidebar_title' => ['required', 'string', 'max:80'],
            'home_search_placeholder' => ['required', 'string', 'max:140'],
            'default_map_lat' => ['required', 'numeric', 'between:-11.5,6.5'],
            'default_map_lng' => ['required', 'numeric', 'between:94.5,141.5'],
            'default_map_radius_km' => ['required', 'in:1,5,10,20'],

            'catalog_hero_title' => ['required', 'string', 'max:100'],
            'catalog_hero_subtitle' => ['nullable', 'string', 'max:320'],
            'campaigns_hero_title' => ['required', 'string', 'max:100'],
            'campaigns_hero_subtitle' => ['nullable', 'string', 'max:320'],

            'max_active_claims_per_user' => ['required', 'integer', 'min:1', 'max:20'],
            'no_show_penalty_threshold' => ['required', 'integer', 'min:1', 'max:20'],
            'no_show_restriction_days' => ['required', 'integer', 'min:1', 'max:90'],
            'esg_co2_per_portion' => ['required', 'numeric', 'min:0', 'max:50'],
            'esg_water_per_portion' => ['required', 'integer', 'min:0', 'max:50000'],
            'esg_value_per_portion' => ['required', 'integer', 'min:0', 'max:10000000'],

            'contact_email' => ['nullable', 'email', 'max:120'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'announcement_enabled' => ['nullable', 'boolean'],
            'announcement_text' => ['nullable', 'string', 'max:280'],
            'announcement_url' => ['nullable', 'url', 'max:255'],
        ]);

        $pairs = [
            'site_name' => trim($validated['site_name']),
            'site_tagline' => trim((string) ($validated['site_tagline'] ?? '')),
            'site_description' => trim((string) ($validated['site_description'] ?? '')),
            'home_title' => trim($validated['home_title']),
            'home_sidebar_title' => trim($validated['home_sidebar_title']),
            'home_search_placeholder' => trim($validated['home_search_placeholder']),
            'default_map_lat' => (string) $validated['default_map_lat'],
            'default_map_lng' => (string) $validated['default_map_lng'],
            'default_map_radius_km' => (string) $validated['default_map_radius_km'],
            'catalog_hero_title' => trim($validated['catalog_hero_title']),
            'catalog_hero_subtitle' => trim((string) ($validated['catalog_hero_subtitle'] ?? '')),
            'campaigns_hero_title' => trim($validated['campaigns_hero_title']),
            'campaigns_hero_subtitle' => trim((string) ($validated['campaigns_hero_subtitle'] ?? '')),
            'max_active_claims_per_user' => (string) $validated['max_active_claims_per_user'],
            'no_show_penalty_threshold' => (string) $validated['no_show_penalty_threshold'],
            'no_show_restriction_days' => (string) $validated['no_show_restriction_days'],
            'esg_co2_per_portion' => (string) $validated['esg_co2_per_portion'],
            'esg_water_per_portion' => (string) $validated['esg_water_per_portion'],
            'esg_value_per_portion' => (string) $validated['esg_value_per_portion'],
            'contact_email' => trim((string) ($validated['contact_email'] ?? '')),
            'contact_whatsapp' => trim((string) ($validated['contact_whatsapp'] ?? '')),
            'announcement_enabled' => $request->boolean('announcement_enabled') ? '1' : '0',
            'announcement_text' => trim((string) ($validated['announcement_text'] ?? '')),
            'announcement_url' => trim((string) ($validated['announcement_url'] ?? '')),
        ];

        if ($request->hasFile('site_logo')) {
            $oldLogo = SiteSetting::get('site_logo_path');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $pairs['site_logo_path'] = $imageService->processAndStore($request->file('site_logo'), 'branding');
        }

        if ($request->hasFile('site_favicon')) {
            $oldFav = SiteSetting::get('site_favicon_path');
            if ($oldFav && Storage::disk('public')->exists($oldFav)) {
                Storage::disk('public')->delete($oldFav);
            }
            $pairs['site_favicon_path'] = $imageService->processAndStore($request->file('site_favicon'), 'branding');
        }

        SiteSetting::setMany($pairs, 'website');

        return back()->with('success', 'Seluruh pengaturan website berhasil disimpan dan langsung diterapkan!');
    }

    /**
     * Restore custom logo/favicon back to default dynamic GPS SVG logo.
     */
    public function resetLogo(Request $request)
    {
        $target = $request->input('target', 'logo'); // logo | favicon | both

        if (in_array($target, ['logo', 'both'], true)) {
            $oldLogo = SiteSetting::get('site_logo_path');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            SiteSetting::setMany(['site_logo_path' => null], 'branding');
        }

        if (in_array($target, ['favicon', 'both'], true)) {
            $oldFav = SiteSetting::get('site_favicon_path');
            if ($oldFav && Storage::disk('public')->exists($oldFav)) {
                Storage::disk('public')->delete($oldFav);
            }
            SiteSetting::setMany(['site_favicon_path' => null], 'branding');
        }

        return back()->with('info', 'Aset brand berhasil dikembalikan ke logo GPS dinamis bawaan.');
    }

    /**
     * Reset all website settings to factory defaults.
     */
    public function resetSettings()
    {
        $oldLogo = SiteSetting::get('site_logo_path');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        $oldFav = SiteSetting::get('site_favicon_path');
        if ($oldFav && Storage::disk('public')->exists($oldFav)) {
            Storage::disk('public')->delete($oldFav);
        }

        SiteSetting::query()->delete();
        SiteSetting::clearCache();

        return back()->with('info', 'Seluruh pengaturan website telah dikembalikan ke setelan awal.');
    }
}
