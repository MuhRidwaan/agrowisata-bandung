<?php

namespace App\Http\Controllers;

use App\Models\PaketTour;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Area;

class FrontendController extends Controller
{
    // ================= HOME =================
    public function home()
    {
        $pakets = PaketTour::with(['vendor.area','reviews','photos'])->latest()->get();
        $areas = Area::all();
        return view('frontend.home', compact('pakets', 'areas'));
    }

    // ================= DETAIL =================
    public function detail($id)
    {
        $paket = PaketTour::with(['vendor', 'reviews'])->findOrFail($id);

        $reviews = Review::with('user')
            ->where('vendor_id', $paket->vendor_id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('frontend.detail', compact('paket', 'reviews'));
    }

    // ================= BOOKING =================
    public function booking($id)
    {
        $paket = PaketTour::findOrFail($id);

        return view('frontend.booking', compact('paket'));
    }

    // ================= PAYMENT =================
    public function payment($id)
    {
        $booking = Booking::findOrFail($id);

        return view('frontend.payment', compact('booking'));
    }

    // ================= SUCCESS =================
    public function success()
    {
        return view('frontend.success');
    }
}