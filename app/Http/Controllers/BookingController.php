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

        // Fitur Search seperti di UserController
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
        $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
        ]);

        $paket = PaketTour::findOrFail($request->paket_tour_id);
        $total = $paket->harga_paket * $request->jumlah_peserta;

        $booking = Booking::create([
            'booking_code' => 'BOOK-' . Str::upper(Str::random(6)),
            'user_id' => auth()->id(),
            'paket_tour_id' => $paket->id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_price' => $total,
            'status' => 'pending',
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking berhasil ditambahkan!');
    }

    public function edit(Booking $booking)
    {
        $pakets = PaketTour::all();
        return view('backend.bookings.form', compact('booking', 'pakets'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        // Kalkulasi ulang total harga jika paket atau jumlah peserta diganti
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        $total = $paket->harga_paket * $request->jumlah_peserta;

        $booking->update([
            'paket_tour_id' => $request->paket_tour_id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_price' => $total,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Data Booking berhasil diupdate!');
    }

    public function destroy(Booking $booking)
    {
        // Hapus payment terkait (jika belum pakai onDelete('cascade') di database)
        if($booking->payment) {
            $booking->payment->delete();
        }
        
        $booking->delete();

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Data Booking berhasil dihapus!');
    }
}