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
        // Data untuk Statistik Utama
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('status', 'paid')->sum('total_price');
        $totalUsers = User::count();
        $totalPaketTours = PaketTour::count();

        // Ambil 5 booking terbaru untuk ditampilkan di tabel
        $recentBookings = Booking::with(['user', 'paketTour'])->latest()->take(5)->get();

        return view('backend.dashboard', compact(
            'totalBookings',
            'totalRevenue',
            'totalUsers',
            'totalPaketTours',
            'recentBookings'
        ));
    }
}
