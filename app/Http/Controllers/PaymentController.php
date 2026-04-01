<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class PaymentController extends Controller
{
    private function configureMidtrans(): void
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }

    private function syncPaymentStatus(Payment $payment): Payment
    {
        if (!$payment->booking) {
            return $payment;
        }

        if (($payment->payment_method ?? null) === 'manual_transfer' || blank($payment->snap_token)) {
            return $payment->fresh(['booking.paketTour', 'booking.user']);
        }

        $shouldSendInvoiceEmail = false;

        try {
            $this->configureMidtrans();
            $status = \Midtrans\Transaction::status($payment->booking->booking_code);
        } catch (\Throwable $e) {
            \Log::warning('Midtrans status sync failed: ' . $e->getMessage(), [
                'booking_code' => $payment->booking->booking_code,
                'payment_id' => $payment->id,
            ]);

            return $payment->fresh(['booking.paketTour', 'booking.user']);
        }

        $transactionStatus = $status->transaction_status ?? null;
        $paymentType = $status->payment_type ?? null;
        $transactionId = $status->transaction_id ?? null;
        $fraudStatus = $status->fraud_status ?? null;

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus !== 'challenge')) {
            if ($payment->status !== 'success' || $payment->booking->status !== 'paid') {
                $payment->update([
                    'status' => 'success',
                    'paid_at' => $payment->paid_at ?? now(),
                    'payment_method' => $paymentType,
                    'transaction_id' => $transactionId,
                ]);

                $payment->booking->update([
                    'status' => 'paid',
                ]);

                TransactionLog::create([
                    'booking_id' => $payment->booking_id,
                    'action' => 'payment_status_synced_success',
                    'new_status' => 'paid',
                    'amount' => $payment->booking->total_price,
                    'payment_method' => $paymentType,
                    'description' => 'Status pembayaran disinkronkan dari Midtrans.',
                    'payload' => json_decode(json_encode($status), true),
                ]);

                $shouldSendInvoiceEmail = true;
            }
        } elseif (in_array($transactionStatus, ['expire', 'cancel', 'deny'], true)) {
            if ($payment->status !== 'failed' || $payment->booking->status !== 'cancelled') {
                $payment->update([
                    'status' => 'failed',
                    'payment_method' => $paymentType,
                    'transaction_id' => $transactionId,
                ]);

                $payment->booking->update([
                    'status' => 'cancelled',
                ]);

                TransactionLog::create([
                    'booking_id' => $payment->booking_id,
                    'action' => 'payment_status_synced_failed',
                    'new_status' => 'cancelled',
                    'amount' => $payment->booking->total_price,
                    'payment_method' => $paymentType,
                    'description' => 'Status pembayaran gagal disinkronkan dari Midtrans: ' . $transactionStatus,
                    'payload' => json_decode(json_encode($status), true),
                ]);
            }
        } else {
            $payment->update([
                'status' => 'pending',
                'payment_method' => $paymentType ?? $payment->payment_method,
                'transaction_id' => $transactionId ?? $payment->transaction_id,
            ]);

            if ($payment->booking->status !== 'pending') {
                $payment->booking->update([
                    'status' => 'pending',
                ]);
            }
        }

        $payment = $payment->fresh(['booking.paketTour', 'booking.user']);

        if ($shouldSendInvoiceEmail && $payment && $payment->status === 'success') {
            $this->triggerInvoiceEmail($payment);
        }

        return $payment;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Payment::with(['booking.paketTour', 'booking.user']);

        // Jika user adalah Vendor, hanya tampilkan pembayaran untuk paket milik mereka
        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            $query->whereHas('booking.paketTour', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

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
        $user = auth()->user();
        $payment = Payment::with(['booking.paketTour', 'booking.user'])->findOrFail($id);

        // Jika user adalah Vendor, pastikan invoice ini milik paket mereka
        if ($user->hasRole('Vendor')) {
            if ($payment->booking->paketTour->vendor_id !== ($user->vendor->id ?? null)) {
                abort(403, 'Akses ditolak.');
            }
        }

        if ($payment->status != 'success') {
            return redirect()->route('payments.index')->with('error', 'Invoice belum tersedia karena pembayaran belum lunas.');
        }

        return view('backend.payments.invoice', compact('payment'));
    }

    public function markAsPaid($id)
    {
        $user = auth()->user();
        $payment = Payment::findOrFail($id);

        // Jika user adalah Vendor, pastikan transaksi ini milik paket mereka
        if ($user->hasRole('Vendor')) {
            if ($payment->booking->paketTour->vendor_id !== ($user->vendor->id ?? null)) {
                abort(403, 'Akses ditolak.');
            }
        }

        if ($payment->status == 'success') {
            return back();
        }

        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
            'payment_method' => $payment->payment_method ?: 'manual_transfer',
        ]);

        $payment->booking->update([
            'status' => 'paid'
        ]);

        // Audit Log
        TransactionLog::create([
            'booking_id' => $payment->booking_id,
            'user_id'    => auth()->id(),
            'action'     => 'payment_confirmed_manual',
            'old_status' => 'pending',
            'new_status' => 'paid',
            'amount'     => $payment->booking->total_price,
            'description'=> "Pembayaran dikonfirmasi lunas secara manual oleh " . auth()->user()->name,
        ]);

        // AUTO KIRIM EMAIL (Manual Mark Paid)
        $this->triggerInvoiceEmail($payment);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi Lunas & Email terkirim!');
    }

    // FUNGSI BARU: Untuk membatalkan pembayaran yang expired
    public function markAsFailed($id)
    {
        $user = auth()->user();
        $payment = Payment::findOrFail($id);

        // Jika user adalah Vendor, pastikan transaksi ini milik paket mereka
        if ($user->hasRole('Vendor')) {
            if ($payment->booking->paketTour->vendor_id !== ($user->vendor->id ?? null)) {
                abort(403, 'Akses ditolak.');
            }
        }

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

                            // Audit Log
                            TransactionLog::create([
                                'booking_id' => $booking->id,
                                'action'     => 'payment_callback_success',
                                'new_status' => 'paid',
                                'amount'     => $request->gross_amount,
                                'payment_method' => $request->payment_type,
                                'description'=> "Pembayaran lunas via Midtrans (" . $request->payment_type . ")",
                                'payload'    => $request->all(),
                            ]);

                            $paymentForEmail = Payment::with(['booking.paketTour', 'booking.user'])->find($booking->payment->id);
                            $this->triggerInvoiceEmail($paymentForEmail);
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

                            // Audit Log
                            TransactionLog::create([
                                'booking_id' => $booking->id,
                                'action'     => 'payment_callback_failed',
                                'new_status' => 'cancelled',
                                'amount'     => $request->gross_amount,
                                'description'=> "Pembayaran gagal/expired via Midtrans: " . $request->transaction_status,
                                'payload'    => $request->all(),
                            ]);
                        }
                    }
                }
            }
        }
        
        return response()->json(['message' => 'Callback handled successfully']);
    }


    public function sendEmail($id)
    {
        $payment = Payment::with(['booking.paketTour', 'booking.user'])->findOrFail($id);

        if ($payment->status != 'success') {
            return back()->with('error', 'Invoice belum bisa dikirim karena pembayaran belum lunas.');
        }

        $emailSent = $this->triggerInvoiceEmail($payment);

  
        if ($emailSent) {
            return back()->with('success', 'Email Invoice berhasil dikirim ulang ke customer.');
        } else {
            return back()->with('error', 'Gagal mengirim email. Pastikan alamat email customer tersedia.');
        }
    }

    private function triggerInvoiceEmail($payment, bool $force = false)
    {
        // Cek apakah fitur email diaktifkan di Global Settings
        if (get_setting('enable_email_notification') !== 'true') {
            return false;
        }

        if (!$force && $payment->invoice_emailed_at) {
            return true;
        }

        // Ambil email dari customer_email, jika kosong ambil dari email user
        $email = $payment->booking->customer_email ?? $payment->booking->user->email ?? null;

        if ($email) {
            try {
                Mail::to($email)->send(new InvoiceMail($payment));
                $payment->forceFill(['invoice_emailed_at' => now()])->saveQuietly();
                return true;
            } catch (\Exception $e) {
               \Log::error('Mail fail: '.$e->getMessage());
            // dd('Penyebab Gagal Kirim Email: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }

    // FUNGSI BARU: Untuk halaman invoice public (customer)
    public function publicInvoice($booking_code)
    {
        $payment = Payment::with(['booking.paketTour.vendor.whatsappsetting', 'booking.user'])
            ->whereHas('booking', function($q) use ($booking_code) {
                $q->where('booking_code', $booking_code);
            })->firstOrFail();

        $payment = $this->syncPaymentStatus($payment);

        return view('frontend.invoice', compact('payment'));
    }

    public function selectPaymentChannel(Request $request, $booking_code)
    {
        $request->validate([
            'selected_channel' => 'required|string|max:255',
        ]);

        $payment = Payment::whereHas('booking', function ($q) use ($booking_code) {
            $q->where('booking_code', $booking_code);
        })->firstOrFail();

        // Validasi channel ada dan aktif
        $channels = collect(json_decode(get_setting('manual_payment_channels', '[]'), true) ?? [])
            ->where('is_active', true);

        $channel = $channels->firstWhere('name', $request->selected_channel);

        if (! $channel) {
            return back()->with('error', 'Channel pembayaran tidak valid.');
        }

        $payment->update(['selected_channel' => $channel['name']]);

        return back()->with('success', 'Metode pembayaran dipilih. Silakan transfer sesuai instruksi.');
    }

    public function uploadTransferProof(Request $request, $booking_code)
    {
        $request->validate([
            'transfer_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'transfer_proof.required' => 'Bukti transfer wajib diunggah.',
            'transfer_proof.image'    => 'File harus berupa gambar.',
            'transfer_proof.mimes'    => 'Format gambar harus JPG, PNG, atau WEBP.',
            'transfer_proof.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $payment = Payment::whereHas('booking', function ($q) use ($booking_code) {
            $q->where('booking_code', $booking_code);
        })->firstOrFail();

        // Hapus file lama jika ada
        if ($payment->transfer_proof) {
            \Storage::disk('public')->delete($payment->transfer_proof);
        }

        $path = $request->file('transfer_proof')->store('transfer_proofs', 'public');

        $payment->update([
            'transfer_proof'             => $path,
            'transfer_proof_uploaded_at' => now(),
        ]);

        return back()->with('success', 'Bukti transfer berhasil diunggah. Silakan tunggu konfirmasi dari admin.');
    }

    public function dispatchFrontendInvoiceEmail($booking_code)
    {
        $payment = Payment::with(['booking.paketTour', 'booking.user'])
            ->whereHas('booking', function ($q) use ($booking_code) {
                $q->where('booking_code', $booking_code);
            })->firstOrFail();

        $payment = $this->syncPaymentStatus($payment);

        if (!$payment || $payment->status !== 'success') {
            return response()->json([
                'sent' => false,
                'status' => $payment->status ?? 'missing',
            ], 202);
        }

        $sent = $this->triggerInvoiceEmail($payment);

        return response()->json([
            'sent' => $sent,
            'status' => $payment->status,
        ]);
    }
}
