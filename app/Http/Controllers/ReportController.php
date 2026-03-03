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
use App\Exports\VendorRevenueExport;
use App\Exports\SalesReportExport;
use App\Exports\BookingReportExport;
use App\Exports\UsersExport;

use App\Models\User;
use App\Models\PaketTour;
use App\Models\TransactionLog;

class ReportController extends Controller
{
    // ================= TRANSACTION LOGS (AUDIT TRAIL) =================
    public function transactionLogs(Request $request)
    {
        // Hanya Super Admin yang boleh melihat audit trail keuangan
        if (!auth()->user()->hasRole('Super Admin')) {
            abort(403, 'Akses ditolak. Hanya Super Admin yang dapat melihat log transaksi.');
        }

        $query = TransactionLog::with(['booking.paketTour', 'user']);

        // Filter by Booking Code
        if ($request->search) {
            $query->whereHas('booking', function($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by Action
        if ($request->action) {
            $query->where('action', $request->action);
        }

        // Filter Tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        return view('backend.reports.transaction_logs', compact('logs'));
    }

    // ================= SALES REPORT =================
    public function salesReport(Request $request)
    {
        $query = Payment::with(['booking.paketTour', 'booking.user'])
            ->where('status', 'success');

        // Jika user adalah Vendor, hanya tampilkan sales untuk vendor mereka
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->whereHas('booking.paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

        // Filter Tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('paid_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $allPayments = $query->latest()->get();
        
        // Handle Excel Export
        if ($request->export == 'excel') {
            return Excel::download(new SalesReportExport($allPayments), 'Sales_Report.xlsx');
        }

        $perPage = $request->input('per_page', 10);
        $payments = $query->paginate($perPage)->withQueryString();
        
        // Hitung Total Revenue dari semua data yang terfilter (bukan hanya yang terpaginasi)
        $totalRevenue = $allPayments->sum(function($payment) {
            return $payment->booking->total_price;
        });

        return view('backend.reports.sales', compact('payments', 'totalRevenue'));
    }

    // ================= BOOKING REPORT =================
    public function bookingReport(Request $request)
    {
        $query = Booking::with(['paketTour', 'user', 'payment']);

        // Jika user adalah Vendor, hanya tampilkan booking untuk vendor mereka
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

        // Filter Tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        // Filter Status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $allBookings = $query->latest()->get();

        // Handle Excel Export
        if ($request->export == 'excel') {
            return Excel::download(new BookingReportExport($allBookings), 'Booking_Report.xlsx');
        }

        $perPage = $request->input('per_page', 10);
        $bookings = $query->paginate($perPage)->withQueryString();

        // Statistics dari all terfilter
        $stats = [
            'total' => $allBookings->count(),
            'pending' => $allBookings->where('status', 'pending')->count(),
            'paid' => $allBookings->where('status', 'paid')->count(),
            'cancelled' => $allBookings->where('status', 'cancelled')->count(),
        ];

        return view('backend.reports.booking', compact('bookings', 'stats'));
    }

    // ================= USER REPORT =================
    public function userReport(Request $request)
    {
        $query = User::query();

        // Filter Tanggal Join
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $allUsers = $query->latest()->get();

        // Handle Excel Export
        if ($request->export == 'excel') {
            return Excel::download(new UsersExport($allUsers), 'User_Report.xlsx');
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage)->withQueryString();

        // Statistics
        $totalUsers = $allUsers->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        return view('backend.reports.user', compact('users', 'totalUsers', 'newUsersThisMonth'));
    }

    // ================= PERFORMANCE REPORT =================
    public function tourPerformanceReport()
    {
        // 1. Top Selling Packages
        $topPackages = PaketTour::withCount(['bookings' => function($query) {
                $query->where('status', 'paid');
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // 2. Vendor Performance
        $vendorPerformance = Vendor::withCount(['paketTours as total_bookings' => function($query) {
                $query->whereHas('bookings', function($b) {
                    $b->where('status', 'paid');
                });
            }])
            ->with(['paketTours.bookings' => function($query) {
                $query->where('status', 'paid');
            }])
            ->get()
            ->map(function($vendor) {
                $totalRevenue = $vendor->paketTours->flatMap->bookings->sum('total_price');
                return [
                    'name' => $vendor->name,
                    'total_bookings' => $vendor->total_bookings,
                    'total_revenue' => $totalRevenue
                ];
            })->sortByDesc('total_bookings');

        // 3. Area Popularity
        $areaPopularity = Area::withCount(['vendors as total_bookings' => function($query) {
                $query->whereHas('paketTours.bookings', function($b) {
                    $b->where('status', 'paid');
                });
            }])
            ->get()
            ->sortByDesc('total_bookings');

        return view('backend.reports.performance', compact('topPackages', 'vendorPerformance', 'areaPopularity'));
    }

    // ================= VENDOR REVENUE REPORT =================
    public function vendorRevenueReport(Request $request)
    {
        $query = Vendor::query();

        // Filter Search by Vendor Name
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $vendors = $query->with(['paketTours.bookings' => function($q) use ($request) {
            $q->where('status', 'paid');
            if ($request->start_date && $request->end_date) {
                $q->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }
        }])->latest()->get();

        $vendorRevenue = $vendors->map(function($vendor) {
            $packageRevenue = $vendor->paketTours->map(function($paket) {
                return [
                    'name' => $paket->nama_paket,
                    'bookings_count' => $paket->bookings->count(),
                    'revenue' => $paket->bookings->sum('total_price')
                ];
            });

            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'total_revenue' => $packageRevenue->sum('revenue'),
                'package_details' => $packageRevenue
            ];
        })->sortByDesc('total_revenue');

        // Handle Excel Export
        if ($request->export == 'excel') {
            return Excel::download(new VendorRevenueExport($vendorRevenue), 'Vendor_Revenue_Report.xlsx');
        }

        // Manual Pagination for Collection
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $vendorRevenue->forPage($page, $perPage),
            $vendorRevenue->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('backend.reports.vendor_revenue', ['vendorRevenue' => $paginatedData]);
    }

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
