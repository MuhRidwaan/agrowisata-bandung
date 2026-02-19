<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.paketTour', 'booking.user']);

        // Fitur Search berdasarkan Kode Booking atau Nama User
        if ($request->search) {
            $query->whereHas('booking', function ($q) use ($request) {
                $q->where('booking_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($u) use ($request) {
                      $u->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Pagination
        $payments = $query->latest()
                          ->paginate(10)
                          ->withQueryString();

        return view('backend.payments.index', compact('payments'));
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
}