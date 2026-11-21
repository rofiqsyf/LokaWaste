<?php

namespace App\Http\Controllers;

use App\Enums\CampaignStatus;
use App\Models\Claim;
use App\Models\FoodCampaign;
use App\Models\FoodListing;
use App\Models\Report;
use App\Models\User;
use App\Models\VolunteerApplication;
use App\Models\VolunteerArea;
use App\Services\CampaignService;
use App\Services\VolunteerApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $report->update([
            'status' => $request->input('status', 'resolved'),
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function users()
    {
        $users = User::with(['district.regency', 'volunteerAreas.district'])
            ->latest()
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function toggleUserVerify(User $user)
    {
        $user->is_verified = !$user->is_verified;
        $user->save();

        return back()->with('success', "Status verifikasi {$user->name} diperbarui.");
    }

    public function toggleUserActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', "Status akun {$user->name} diperbarui.");
    }
}
