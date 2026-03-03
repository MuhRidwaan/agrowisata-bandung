<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\PaketTour;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Area;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    // ================= HOME =================
    public function home(Request $request)
    {
        $query = PaketTour::with(['vendor.area','reviews','photos'])
            ->whereHas('photos')
            ->latest();

        if ($request->area && $request->area != 'all') {
            $query->whereHas('vendor.area', function ($q) use ($request) {
                $q->whereRaw('LOWER(name) = ?', [strtolower($request->area)]);
            });
        }

        if ($request->search) {
            $query->whereRaw('LOWER(nama_paket) LIKE ?', [
                '%' . strtolower($request->search) . '%'
            ]);
        }

        $pakets = $query->get();
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
        $paket = PaketTour::with(['pricingRules', 'vendor.area', 'photos', 'tanggalAvailables'])->findOrFail($id);

        // Ambil tanggal available yang aktif dan belum lewat
        $availableDates = $paket->tanggalAvailables()
            ->where('status', 'aktif')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->get();

        return view('frontend.booking', compact('paket', 'availableDates'));
    }

    // ================= STORE BOOKING (Frontend AJAX) =================
    public function storeBooking(Request $request)
    {
        $request->validate([
            'paket_tour_id'  => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'visit_date'     => 'required|date|after_or_equal:today',
        ]);

        // Kalkulasi harga menggunakan logic existing PaketTour::calculatePrice()
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        $pricing = $paket->calculatePrice($request->jumlah_peserta);
        $total = $pricing['total_price'];

        // Generate booking code
        $prefix = get_setting('booking_prefix', 'BOOK-');
        $bookingCode = $prefix . Str::upper(Str::random(6));

        // Simpan booking
        $booking = Booking::create([
            'booking_code'   => $bookingCode,
            'user_id'        => auth()->id() ?? null,
            'paket_tour_id'  => $paket->id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_price'    => $total,
            'status'         => 'pending',
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'visit_date'     => $request->visit_date,
        ]);

        // Konfigurasi & buat Snap Token Midtrans
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id'     => $bookingCode,
                    'gross_amount' => (int) $total,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email'      => $request->customer_email,
                    'phone'      => $request->customer_phone,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan payment
            Payment::create([
                'booking_id' => $booking->id,
                'status'     => 'pending',
                'snap_token' => $snapToken,
            ]);

            return response()->json([
                'success'      => true,
                'booking_code' => $bookingCode,
                'snap_token'   => $snapToken,
                'total_price'  => $total,
            ]);

        } catch (\Exception $e) {
            // Jika gagal generate token, booking tetap tersimpan tapi payment belum ada
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungkan ke layanan pembayaran: ' . $e->getMessage(),
            ], 500);
        }
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