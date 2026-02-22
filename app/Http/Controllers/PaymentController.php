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

    public function invoice($id)
    {
        $payment = Payment::with(['booking.paketTour', 'booking.user'])->findOrFail($id);

        if ($payment->status != 'success') {
            return redirect()->route('payments.index')->with('error', 'Invoice belum tersedia karena pembayaran belum lunas.');
        }

        return view('backend.payments.invoice', compact('payment'));
    }

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

    // FUNGSI BARU: Untuk membatalkan pembayaran yang expired
    public function markAsFailed($id)
    {
        $payment = Payment::findOrFail($id);

        if ($payment->status != 'success') {
            $payment->update([
                'status' => 'failed'
            ]);

            $payment->booking->update([
                'status' => 'cancelled'
            ]);
        }

        return back()->with('success', 'Transaksi berhasil dibatalkan karena Expired.');
    }

    // UPDATE: Webhook ditambahkan logika untuk Expire/Cancel
    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            $booking = Booking::where('booking_code', $request->order_id)->first();
            
            if ($booking) {
                // Jika Lunas
                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    if ($booking->status != 'paid') {
                        $booking->update(['status' => 'paid']);
                        if ($booking->payment) {
                            $booking->payment->update([
                                'status' => 'success',
                                'paid_at' => now(),
                                'payment_method' => $request->payment_type
                            ]);
                        }
                    }
                } 
                // Jika Expired, Batal, atau Ditolak
                elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {
                    if ($booking->status != 'cancelled') {
                        $booking->update(['status' => 'cancelled']);
                        if ($booking->payment) {
                            $booking->payment->update([
                                'status' => 'failed'
                            ]);
                        }
                    }
                }
            }
        }
        
        return response()->json(['message' => 'Callback handled successfully']);
    }
}