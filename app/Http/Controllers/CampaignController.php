<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Models\CampaignUpdate;
use App\Models\District;
use App\Models\FoodCampaign;
use App\Services\CampaignService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $campaignService,
        protected ImageUploadService $imageService
    ) {}

    public function index(Request $request)
    {
        $query = FoodCampaign::with(['district.regency', 'user'])
            ->where('status', CampaignStatus::PUBLISHED->value);

        if ($request->filled('beneficiary_type')) {
            $query->where('beneficiary_type', $request->input('beneficiary_type'));
        }

        if ($request->filled('regency_id')) {
            $query->whereHas('district', fn($q) => $q->where('regency_id', $request->input('regency_id')));
        }

        $campaigns = $query->latest()->paginate(12);

        return view('campaigns.index', compact('campaigns'));
    }

    public function show(string $slug)
    {
        $campaign = FoodCampaign::where('slug', $slug)
            ->with(['user', 'district.regency.province', 'updates.user'])
            ->firstOrFail();

        // Guest / non-volunteer cannot view draft or pending_review (TC-35)
        if (!in_array($campaign->status, [CampaignStatus::PUBLISHED->value, CampaignStatus::CLOSED->value])) {
            $user = Auth::user();
            abort_unless($user && ($user->id === $campaign->user_id || $user->isAdmin()), 403, 'Galang makanan belum dipublikasikan.');
        }

        return view('campaigns.show', compact('campaign'));
    }

    public function volunteerIndex()
    {
        $user = Auth::user();
        abort_unless($user->isVolunteer(), 403, 'Menu ini hanya untuk relawan terdaftar.');

        $campaigns = FoodCampaign::where('user_id', $user->id)
            ->with(['district.regency', 'updates'])
            ->latest()
            ->get();

        return view('volunteer.campaigns.index', compact('campaigns', 'user'));
    }

    public function create()
    {
        $user = Auth::user();
        abort_unless($user->isVolunteer(), 403, 'Hanya relawan yang dapat membuat galang makanan.');

        // Volunteer can only choose approved districts (TC-28, SDD 3.7 Rule 22)
        $approvedDistrictIds = $user->approvedDistrictIds();
        $districts = District::whereIn('id', $approvedDistrictIds)->with('regency')->get();

        return view('volunteer.campaigns.create', compact('districts', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isVolunteer(), 403, 'Akses ditolak.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'district_id' => ['required', 'exists:districts,id'],
            'beneficiary_type' => ['required', 'in:panti_asuhan,ponpes,panti_jompo,tunawisma,lainnya'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-11.5,6.5'],
            'longitude' => ['required', 'numeric', 'between:94.5,141.5'],
            'location_precision' => ['required', 'in:exact,area'],
            'daily_need' => ['nullable', 'string', 'max:255'],
            'target_portions' => ['required', 'integer', 'min:1'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'body' => ['required', 'string', 'min:20'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ]);

        abort_unless($user->isVolunteerIn($validated['district_id']), 403, 'Anda bukan relawan terdaftar di kecamatan ini.');

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $this->imageService->processAndStore($request->file('cover'), 'campaigns');
        }

        $validated['cover_path'] = $coverPath;

        try {
            $campaign = $this->campaignService->create($user, $validated);

            // Check if pin is far from district center (> 15km warning) (TC-29, SDD 3.7 Rule 23)
            $warning = $this->campaignService->checkDistrictCenterWarning(
                $campaign->district_id,
                $campaign->latitude,
                $campaign->longitude
            );

            $msg = 'Draf galang makanan berhasil dibuat!';
            if ($warning) {
                return redirect()->route('volunteer.campaigns.index')->with('warning', "{$msg} Namun ada peringatan: {$warning}");
            }

            return redirect()->route('volunteer.campaigns.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function submit(FoodCampaign $campaign)
    {
        $user = Auth::user();

        try {
            $this->campaignService->submitForReview($campaign, $user);
            return back()->with('success', 'Galang makanan telah diajukan untuk ditinjau admin. Anda akan menerima pembaruan segera.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function postUpdate(FoodCampaign $campaign, Request $request)
    {
        $user = Auth::user();
        abort_unless($user->id === $campaign->user_id, 403, 'Akses ditolak.');

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:5'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $this->imageService->processAndStore($request->file('photo'), 'campaigns/updates');
        }

        CampaignUpdate::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'body' => $validated['body'],
            'photo_path' => $photoPath,
        ]);

        return back()->with('success', 'Pembaruan penyaluran berhasil dipublikasikan!');
    }

    public function close(FoodCampaign $campaign)
    {
        $user = Auth::user();

        try {
            $this->campaignService->close($campaign, $user);
            return back()->with('info', 'Galang makanan telah resmi ditutup. Terima kasih atas perjuangan Anda!');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
