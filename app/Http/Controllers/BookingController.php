<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaketTour;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['paketTour', 'user', 'payment']);

        // Fitur Search berdasarkan kode booking atau nama user
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($u) use ($request) {
                      $u->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Pagination
        $bookings = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $pakets = PaketTour::all();
        return view('backend.bookings.form', compact('pakets'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input termasuk data diri
        $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Kalkulasi Total Harga
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        $total = $paket->harga_paket * $request->jumlah_peserta;
        $bookingCode = 'BOOK-' . Str::upper(Str::random(6));

        // 3. Simpan Data Booking
        $booking = Booking::create([
            'booking_code'   => $bookingCode,
            'user_id'        => auth()->id(),
            'paket_tour_id'  => $paket->id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_price'    => $total,
            'status'         => 'pending',
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
        ]);

        // 4. Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

        // 5. Susun Parameter untuk Midtrans
        $params = array(
            'transaction_details' => array(
                'order_id'     => $bookingCode,
                'gross_amount' => $total,
            ),
            'customer_details' => array(
                'first_name' => $request->customer_name,
                'email'      => $request->customer_email,
                'phone'      => $request->customer_phone,
            ),
        );

        // 6. Dapatkan Snap Token dari Midtrans
        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // 7. Simpan Data Payment
        Payment::create([
            'booking_id' => $booking->id,
            'status'     => 'pending',
            'snap_token' => $snapToken,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking berhasil ditambahkan! Silakan lakukan pembayaran.');
    }

    public function edit(Booking $booking)
    {
        $pakets = PaketTour::all();
        return view('backend.bookings.form', compact('booking', 'pakets'));
    }

    public function update(Request $request, Booking $booking)
    {
        // Validasi Edit
        $request->validate([
            'paket_tour_id'  => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'status'         => 'required|in:pending,confirmed,cancelled,paid',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // Kalkulasi ulang jika paket atau jumlah peserta diubah
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        $total = $paket->harga_paket * $request->jumlah_peserta;

        $booking->update([
            'paket_tour_id'  => $request->paket_tour_id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_price'    => $total,
            'status'         => $request->status,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Data Booking berhasil diupdate!');
    }

    public function destroy(Booking $booking)
    {
        // Hapus payment terkait agar tidak error foreign key
        if($booking->payment) {
            $booking->payment->delete();
        }
        
        $booking->delete();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Data Booking berhasil dihapus permanen!');
    }
}