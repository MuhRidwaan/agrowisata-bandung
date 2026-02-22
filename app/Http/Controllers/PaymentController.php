<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.paketTour', 'booking.user']);

        if ($request->search) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($u) use ($request) {
                      $u->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $payments = $query->latest()->paginate(10)->withQueryString();

        return view('backend.payments.index', compact('payments'));
    }

    // Fungsi untuk menampilkan halaman Invoice
    public function invoice($id)
    {
        $payment = Payment::with(['booking.paketTour', 'booking.user'])->findOrFail($id);

        // Cegah user akses invoice kalau belum lunas
        if ($payment->status != 'success') {
            return redirect()->route('payments.index')->with('error', 'Invoice belum tersedia karena pembayaran belum lunas.');
        }

        return view('backend.payments.invoice', compact('payment'));
    }

    // Fungsi manual/frontend trick untuk konfirmasi lunas
    public function markAsPaid($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status == 'success') {
            return back();
        }

        $payment->update([
            'status' => 'success',
            'paid_at' => now()
        ]);

        $payment->booking->update([
            'status' => 'paid'
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi Lunas!');
    }

    // Fungsi Webhook / Callback untuk Midtrans Server-to-Server
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        
        // Verifikasi keaslian notifikasi dari Midtrans (Keamanan)
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                
                $booking = Booking::where('booking_code', $request->order_id)->first();
                
                if ($booking && $booking->status != 'paid') {
                    // Update Booking
                    $booking->update(['status' => 'paid']);
                    
                    // Update Payment
                    if ($booking->payment) {
                        $booking->payment->update([
                            'status' => 'success',
                            'paid_at' => now(),
                            'payment_method' => $request->payment_type
                        ]);
                    }
                }
            }
        }
        
        // Kasih balasan ke Midtrans kalau kita udah nerima notifnya
        return response()->json(['message' => 'Callback handled successfully']);
    }
}