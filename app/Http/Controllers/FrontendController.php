<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\PaketTour;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Area;
use App\Models\TransactionLog;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    // ================= HOME =================
    public function home(Request $request)
    {
        $query = PaketTour::with([
                'vendor.area',
                'photos',
                'reviews' => fn ($q) => $q->where('status', 'approved')->with('photos'),
            ])
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
        $paket = PaketTour::with([
            'vendor.area',
            'photos',
            'pricingRules',
            'reviews' => fn ($q) => $q->where('status', 'approved')->with(['photos', 'user'])->latest(),
        ])->findOrFail($id);

        return view('frontend.detail', compact('paket'));
    }

    // ================= BOOKING =================
    public function booking($id)
    {
        $paket = PaketTour::with(['pricingRules', 'vendor.area', 'vendor.whatsappsetting', 'photos', 'tanggalAvailables', 'umkmProducts.photos'])->findOrFail($id);

        // Ambil tanggal available yang aktif dan belum lewat
        $availableDates = $paket->tanggalAvailables()
            ->where('status', 'aktif')
            ->where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->get()
            ->map(function ($tgl) use ($paket) {
                // Hitung kuota terpakai dari bookings yang tidak dibatalkan
                $used = Booking::where('paket_tour_id', $paket->id)
                    ->where('created_at', $tgl->tanggal)
                    ->where('status', '!=', 'cancelled')
                    ->sum('jumlah_peserta');
                $tgl->quota_used = (int) $used;
                $tgl->sisa = max(0, $tgl->kuota - $used);
                return $tgl;
            });

        return view('frontend.booking', compact('paket', 'availableDates'));
    }

    // ================= STORE BOOKING (Frontend AJAX) =================
    public function storeBooking(Request $request)
    {
        $request->validate([
            'paket_tour_id'  => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'use_bundling'   => 'nullable|boolean',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'visit_date'     => 'required|date|after_or_equal:today',
        ]);

        // Validasi kuota terhadap tanggal yang dipilih
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        $tanggalAvailable = $paket->tanggalAvailables()
            ->where('status', 'aktif')
            ->whereDate('tanggal', $request->visit_date)
            ->first();

        if (!$tanggalAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kunjungan tidak tersedia untuk paket ini.',
            ], 422);
        }

        $used = Booking::where('paket_tour_id', $paket->id)
            ->whereDate('created_at', $request->visit_date)
            ->where('status', '!=', 'cancelled')
            ->sum('jumlah_peserta');

        $remaining = max(0, (int) $tanggalAvailable->kuota - (int) $used);
        if ((int) $request->jumlah_peserta > $remaining) {
            return response()->json([
                'success' => false,
                'message' => "Kuota tidak mencukupi. Sisa kuota untuk tanggal ini: {$remaining} orang.",
            ], 422);
        }

        // Kalkulasi harga menggunakan logic existing PaketTour::calculatePrice()
        $pricing = $paket->calculatePrice($request->jumlah_peserta, $request->boolean('use_bundling'));
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

        // Audit Log
        TransactionLog::create([
            'booking_id' => $booking->id,
            'user_id'    => auth()->id() ?? null,
            'action'     => 'booking_created_frontend',
            'new_status' => 'pending',
            'amount'     => $total,
            'description'=> "Booking baru dibuat dari Frontend oleh Customer " . $request->customer_name,
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

    // ================= RESUME PAYMENT =================
    public function resumePayment($booking_code)
    {
        $booking = Booking::with(['payment', 'paketTour.vendor.area'])
            ->where('booking_code', $booking_code)
            ->firstOrFail();

        $payment = $booking->payment;

        // Jika sudah lunas, langsung arahkan ke invoice
        if ($booking->status === 'paid' || ($payment && $payment->status === 'success')) {
            return redirect()->route('frontend.invoice', $booking->booking_code)
                ->with('success', 'Pembayaran sudah berhasil. Berikut invoice Anda.');
        }

        // Jika belum ada payment/snap token, tampilkan error
        if (!$payment || empty($payment->snap_token)) {
            return redirect()->route('home')
                ->with('error', 'Data pembayaran tidak ditemukan. Silakan lakukan booking ulang.');
        }

        return view('frontend.resume_payment', compact('booking', 'payment'));
    }

    // ================= PENDING BOOKING STATUS =================
    public function pendingBookingStatus($booking_code)
    {
        $booking = Booking::with('payment')
            ->where('booking_code', $booking_code)
            ->first();

        if (!$booking || !$booking->payment) {
            return response()->json([
                'active' => false,
                'reason' => 'missing',
            ]);
        }

        if (in_array($booking->status, ['paid', 'cancelled'], true)) {
            return response()->json([
                'active' => false,
                'reason' => $booking->status,
            ]);
        }

        if (in_array($booking->payment->status, ['success', 'failed'], true)) {
            return response()->json([
                'active' => false,
                'reason' => $booking->payment->status,
            ]);
        }

        if (empty($booking->payment->snap_token)) {
            return response()->json([
                'active' => false,
                'reason' => 'missing_snap_token',
            ]);
        }

        return response()->json([
            'active' => true,
            'booking_code' => $booking->booking_code,
            'resume_url' => route('payment.resume', $booking->booking_code),
            'total_price' => $booking->total_price,
        ]);
    }

    // ================= SUCCESS =================
    public function success()
    {
        return view('frontend.success');
    }
}
