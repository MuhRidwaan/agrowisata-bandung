<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PaketTour;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $vendorId = $user->hasRole('Vendor') ? ($user->vendor->id ?? null) : null;

        // Data untuk Statistik Utama
        $queryBooking = Booking::query();
        $queryRevenue = Booking::where('status', 'paid');
        $queryPaket = PaketTour::query();

        if ($vendorId) {
            $queryBooking->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
            $queryRevenue->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
            $queryPaket->where('vendor_id', $vendorId);
        }

        $totalBookings = $queryBooking->count();
        $totalRevenue = $queryRevenue->sum('total_price');
        $totalUsers = User::count(); // Tetap total user untuk dashboard
        $totalPaketTours = $queryPaket->count();

        // Ambil 5 booking terbaru
        $recentQuery = Booking::with(['user', 'paketTour']);
        if ($vendorId) {
            $recentQuery->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }
        $recentBookings = $recentQuery->latest()->take(5)->get();

        return view('backend.dashboard', compact(
            'totalBookings',
            'totalRevenue',
            'totalUsers',
            'totalPaketTours',
            'recentBookings'
        ));
    }
}
