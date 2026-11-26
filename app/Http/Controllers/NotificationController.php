<?php

namespace App\Http\Controllers;

use App\Notifications\LokaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of user notifications.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Seed initial notifications if the user has none yet
        if ($user->notifications()->count() === 0) {
            $this->seedInitialNotifications($user);
        }

        $filter = $request->query('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'claim') {
            $query->whereIn('data->category', ['claim', 'listing']);
        } elseif ($filter === 'system') {
            $query->whereIn('data->category', ['system', 'volunteer', 'campaign']);
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();

        // Aggregate tab counts
        $totalCount = $user->notifications()->count();
        $unreadCount = $user->unreadNotifications()->count();
        $claimCount = $user->notifications()->whereIn('data->category', ['claim', 'listing'])->count();
        $systemCount = $user->notifications()->whereIn('data->category', ['system', 'volunteer', 'campaign'])->count();

        return view('notifications.index', compact(
            'notifications',
            'filter',
            'totalCount',
            'unreadCount',
            'claimCount',
            'systemCount'
        ));
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi telah ditandai dibaca.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Delete all read notifications.
     */
    public function destroyAllRead(Request $request)
    {
        $user = Auth::user();
        $user->readNotifications()->delete();

        return back()->with('success', 'Semua notifikasi yang telah dibaca berhasil dibersihkan.');
    }

    /**
     * Generate realistic context-aware notifications for user when table is empty.
     */
    protected function seedInitialNotifications($user): void
    {
        // 1. Welcome to LokaWaste
        $user->notify(new LokaNotification(
            title: 'Selamat Datang di LokaWaste!',
            message: 'Akun Anda telah aktif. Mari selamatkan pangan berlebih di sekitar Anda, dukung gerakan zero waste, dan kurangi jejak emisi karbon bersama komunitas.',
            category: 'system',
            icon: 'sparkles',
            actionUrl: route('home'),
            actionLabel: 'Jelajah Peta Pangan'
        ));

        // 2. Add notifications for existing incoming claims on user's listings
        $incomingClaims = \App\Models\Claim::with('listing', 'user')
            ->whereHas('listing', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->take(3)
            ->get();

        foreach ($incomingClaims as $claim) {
            $user->notify(new LokaNotification(
                title: 'Pengajuan Klaim: ' . ($claim->listing?->title ?? 'Makanan'),
                message: ($claim->user?->name ?? 'Pengguna') . " mengajukan klaim {$claim->portions_requested} porsi untuk makanan berlebih Anda.",
                category: 'claim',
                icon: 'shopping-bag',
                actionUrl: route('dashboard', ['tab' => 'dibagikan']),
                actionLabel: 'Kelola Klaim di Dashboard',
                meta: ['claim_id' => $claim->id, 'listing_id' => $claim->listing_id]
            ));
        }

        // 3. Add notifications for claims made by user
        $myClaims = \App\Models\Claim::with('listing.user')
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        foreach ($myClaims as $claim) {
            if ($claim->status === 'approved') {
                $user->notify(new LokaNotification(
                    title: 'Klaim Disetujui: ' . ($claim->listing?->title ?? 'Makanan'),
                    message: "Pemberi makanan telah menyetujui klaim {$claim->portions_approved} porsi. Alamat lengkap dan kontak kini telah terbuka.",
                    category: 'claim',
                    icon: 'check-circle',
                    actionUrl: route('listings.show', $claim->listing_id),
                    actionLabel: 'Lihat Titik & Alamat Presisi',
                    meta: ['claim_id' => $claim->id]
                ));
            } elseif ($claim->status === 'picked_up') {
                $user->notify(new LokaNotification(
                    title: 'Pengambilan Selesai: ' . ($claim->listing?->title ?? 'Makanan'),
                    message: "Penyelamatan pangan berhasil diselesaikan! Porsi telah diterima dengan baik. Bagikan ulasan Anda.",
                    category: 'claim',
                    icon: 'sparkles',
                    actionUrl: route('listings.show', $claim->listing_id),
                    actionLabel: 'Tulis Ulasan',
                    meta: ['claim_id' => $claim->id]
                ));
            }
        }
    }
}
