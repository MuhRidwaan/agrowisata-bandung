<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Area;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // ================= FILTER TANGGAL =================
        $reviewQuery = Review::query();

        if ($request->start && $request->end) {
            $reviewQuery->whereBetween('created_at', [$request->start, $request->end]);
        }

        // ================= SUMMARY =================
        $totalVendor = Vendor::count();
        $totalReview = $reviewQuery->count();
        $avgRating = $reviewQuery->avg('rating');

        // ================= TOP VENDOR =================
        $topVendor = Vendor::withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->first();

        // ================= OPTIONAL =================
        $totalBooking = class_exists(Booking::class) ? Booking::count() : 0;
        $totalPayment = class_exists(Payment::class) ? Payment::sum('amount') : 0;

        // ================= CHART =================
        $chartData = Vendor::withCount('reviews')->get();

        // ================= REPORT PER AREA =================
        $areaData = Area::withCount('vendors')->get();

        return view('report.index', compact(
            'totalVendor',
            'totalReview',
            'avgRating',
            'topVendor',
            'totalBooking',
            'totalPayment',
            'chartData',
            'areaData'
        ));
    }

    // ================= EXPORT PDF =================
    public function exportPDF()
    {
        $data = Vendor::withCount('reviews')->get();
        $pdf = Pdf::loadView('report.pdf', compact('data'));
        return $pdf->download('Report Vendor.pdf');
    }

    // ================= EXPORT EXCEL =================
    public function exportExcel()
    {
        $data = Vendor::withCount('reviews')->get();

        return Excel::download(new ReportExport, 'Report Vendor.xlsx');

    }
}
