<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaketTour;
use App\Models\Payment;
use App\Models\TransactionLog;
use App\Models\UmkmProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['paketTour', 'user', 'payment']);

        // Jika user adalah Vendor, hanya tampilkan booking untuk paket milik mereka
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

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
        $query = PaketTour::query()->has('photos');

        // Jika user Vendor, hanya tampilkan paket milik vendor tersebut
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
        }

        $pakets = $query->orderBy('nama_paket')->get();
        return view('backend.bookings.form', compact('pakets'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input termasuk data diri
        $request->validate([
            'paket_tour_id'  => 'required|exists:paket_tours,id',
            'jumlah_peserta' => 'required|integer|min:1',
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'visit_date'     => 'required|date|after_or_equal:today',
            'umkm_products'  => 'nullable|string',
        ]);

        // 2. Kalkulasi Total Harga menggunakan Pricing Rules
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        if (!$paket->photos()->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Paket tour belum memiliki foto, sehingga belum bisa dipilih untuk booking.');
        }

        $pricing = $paket->calculatePrice($request->jumlah_peserta);
        $total = $pricing['total_price'];

        $umkmData = $this->resolveUmkmItems($paket, $request->umkm_products);
        $total += $umkmData['addon_total'];
        $prefix = get_setting('booking_prefix', 'BOOK-');
        $bookingCode = $prefix . Str::upper(Str::random(6));

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
            'visit_date'     => $request->visit_date,
        ]);
        
        if (!empty($umkmData['sync_data'])) {
            $booking->umkmProducts()->sync($umkmData['sync_data']);
        }
        // Audit Log
        TransactionLog::create([
            'booking_id' => $booking->id,
            'user_id'    => auth()->id(),
            'action'     => 'booking_created',
            'new_status' => 'pending',
            'amount'     => $total,
            'description'=> "Booking baru dibuat oleh " . auth()->user()->name,
        ]);

        // 4. Konfigurasi Midtrans
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            // 5. Susun Parameter untuk Midtrans
            $params = array(
                'transaction_details' => array(
                    'order_id'     => $bookingCode,
                    'gross_amount' => (int) $total,
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

        } catch (\Exception $e) {
            return redirect()
                ->route('bookings.index')
                ->with('error', 'Booking tersimpan, namun gagal menghubungkan ke Midtrans: ' . $e->getMessage());
        }
    }

    public function edit(Booking $booking)
    {
        $query = PaketTour::query()->has('photos');

        // Jika user Vendor, hanya tampilkan paket milik vendor tersebut
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
        }

        $pakets = $query->orderBy('nama_paket')->get();
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
            'visit_date'     => 'required|date',
            'umkm_products' => 'nullable|string',
        ]);

        // Kalkulasi ulang jika paket atau jumlah peserta diubah
        $paket = PaketTour::findOrFail($request->paket_tour_id);
        if (!$paket->photos()->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Paket tour belum memiliki foto, sehingga belum bisa dipilih untuk booking.');
        }

        $pricing = $paket->calculatePrice($request->jumlah_peserta);
        $total = $pricing['total_price'];

        $umkmData = $this->resolveUmkmItems($paket, $request->umkm_products);
        $total += $umkmData['addon_total'];

        $oldStatus = $booking->status;
        $booking->update([
            'paket_tour_id'  => $request->paket_tour_id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_price'    => $total,
            'status'         => $request->status,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'visit_date'     => $request->visit_date,
        ]);

        if (!empty($umkmData['sync_data'])) {
            $booking->umkmProducts()->sync($umkmData['sync_data']);
        } else {
            $booking->umkmProducts()->detach();
        }

        // Audit Log if status changed
        if($oldStatus != $request->status) {
            TransactionLog::create([
                'booking_id' => $booking->id,
                'user_id'    => auth()->id(),
                'action'     => 'status_updated',
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'description'=> "Status booking diubah secara manual oleh " . auth()->user()->name,
            ]);
        }

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

    private function resolveUmkmItems(PaketTour $paket, ?string $umkmProductsJson): array
    {
        $items = json_decode($umkmProductsJson ?? '[]', true);

        if (!is_array($items) || empty($items)) {
            return [
                'addon_total' => 0,
                'sync_data' => [],
            ];
        }

        $requestedItems = collect($items)
            ->filter(fn ($item) => is_array($item) && isset($item['id'], $item['qty']))
            ->map(function ($item) {
                return [
                    'id' => (int) $item['id'],
                    'qty' => max(0, (int) $item['qty']),
                ];
            })
            ->filter(fn ($item) => $item['id'] > 0 && $item['qty'] > 0)
            ->values();

        if ($requestedItems->isEmpty()) {
            return [
                'addon_total' => 0,
                'sync_data' => [],
            ];
        }

        $allowedProducts = $paket->umkmProducts()
            ->whereIn('umkm_products.id', $requestedItems->pluck('id')->all())
            ->get()
            ->keyBy('id');

        $addonTotal = 0;
        $syncData = [];

        foreach ($requestedItems as $item) {
            /** @var UmkmProduct|null $product */
            $product = $allowedProducts->get($item['id']);

            if (! $product) {
                continue;
            }

            $addonTotal += $product->price * $item['qty'];
            $syncData[$product->id] = [
                'quantity' => $item['qty'],
                'price' => $product->price,
            ];
        }

        return [
            'addon_total' => $addonTotal,
            'sync_data' => $syncData,
        ];
    }
}
