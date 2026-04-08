<!DOCTYPE html>
<html>

<head>
    <title>Invoice Pembayaran - Agrotourism Bandung</title>
</head>

<body
    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f9; margin: 0; padding: 20px;">
    @php
        $booking = $payment->booking;
        $paket = $booking->paketTour;
        $vendor = $paket->vendor ?? null;
        $whatsappSetting = $vendor?->whatsappsetting;
        $waNumber = preg_replace('/[^0-9]/', '', $whatsappSetting?->phone_number ?? '');
        $waTemplate = $whatsappSetting?->message_template
            ? str_replace('{{nama}}', $vendor->name ?? 'Admin', $whatsappSetting->message_template)
            : 'Halo, saya sudah melakukan pemesanan ' . ($paket->nama_paket ?? 'paket wisata') . ' dengan kode booking ' . $booking->booking_code . '.';
        $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . urlencode($waTemplate) : null;
    @endphp

    <div
        style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

        <div style="border-bottom: 2px solid #28a745; padding-bottom: 15px; margin-bottom: 20px;">
            <h2 style="color: #28a745; margin: 0;">Agrowisata Tour</h2>
            <p style="margin: 0; color: #666; font-size: 14px;">Invoice Pembayaran Lunas</p>
        </div>

        <p>Halo, <strong>{{ $payment->booking->customer_name ?? $payment->booking->user->name }}</strong>,</p>
        <p>Terima kasih atas pembayaran Anda. Berikut adalah rincian lengkap transaksi Anda yang sudah kami terima:</p>

        <table style="width: 100%; margin-bottom: 20px; font-size: 14px;">
            <tr>
                <td style="padding: 5px 0;"><strong>Kode Booking:</strong></td>
                <td style="padding: 5px 0; text-align: right;">{{ $payment->booking->booking_code }}</td>
            </tr>
            @if (!empty($payment->booking->visit_date) || !empty($payment->booking->tanggal))
                <tr>
                    <td style="padding: 5px 0;"><strong>Tanggal Kunjungan:</strong></td>
                    <td style="padding: 5px 0; text-align: right;">
                        {{ \Carbon\Carbon::parse($payment->booking->visit_date ?? $payment->booking->tanggal)->format('d M Y') }}
                    </td>
                </tr>
            @endif
            <tr>
                <td style="padding: 5px 0;"><strong>Tanggal Pembayaran:</strong></td>
                <td style="padding: 5px 0; text-align: right;">
                    {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>Metode Pembayaran:</strong></td>
                <td style="padding: 5px 0; text-align: right;">
                    {{ strtoupper($payment->payment_method ?? 'Gateway/Transfer') }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;"><strong>Status:</strong></td>
                <td style="padding: 5px 0; text-align: right;"><span
                        style="background-color: #28a745; color: white; padding: 3px 8px; border-radius: 3px; font-size: 12px;">LUNAS</span>
                </td>
            </tr>
        </table>

        <h4 style="margin-bottom: 10px; color: #444; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Rincian
            Pesanan</h4>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Paket Tour</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Peserta</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $pax = (int) ($booking->jumlah_peserta ?? 0);
                    $hargaNormal = (float) ($paket->harga_paket ?? 0);
                    $baseTotal = $hargaNormal * $pax;
                    $umkmItems = $booking->umkmProducts ?? collect();
                    $umkmTotal = $umkmItems->sum(function ($product) {
                        $qty = (int) ($product->pivot->quantity ?? 0);
                        $price = (float) ($product->pivot->price ?? $product->price ?? 0);
                        return $price * $qty;
                    });
                    $tourPaid = max(0, (float) ($booking->total_price ?? 0) - $umkmTotal);
                    $discount = max(0, $baseTotal - $tourPaid);
                @endphp
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        {{ $paket->nama_paket ?? '-' }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                        {{ $pax }} Pax</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp
                        {{ number_format($tourPaid, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                @if ($discount > 0)
                <tr>
                    <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right; color: #dc3545;"><strong>Potongan Diskon:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right; color: #dc3545;"><strong>-Rp
                            {{ number_format($discount, 0, ',', '.') }}</strong></td>
                </tr>
                @endif
                @if ($umkmTotal > 0)
                    <tr>
                        <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right;"><strong>Total Produk UMKM:</strong></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;"><strong>Rp
                                {{ number_format($umkmTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right; background-color: #f8f9fa;"><strong>Total
                            Bayar:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right; color: #28a745; background-color: #f8f9fa;"><strong>Rp
                            {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>

        @if (($umkmItems ?? collect())->isNotEmpty())
            <h4 style="margin: 0 0 10px 0; color: #444; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Produk UMKM (Tambahan)</h4>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Produk</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Qty</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Harga</th>
                        <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($umkmItems as $product)
                        @php
                            $qty = (int) ($product->pivot->quantity ?? 0);
                            $price = (float) ($product->pivot->price ?? $product->price ?? 0);
                            $lineTotal = $qty * $price;
                        @endphp
                        <tr>
                            <td style="padding: 10px; border: 1px solid #ddd;">{{ $product->name ?? '-' }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $qty }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp {{ number_format($price, 0, ',', '.') }}</td>
                            <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp {{ number_format($lineTotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div
            style="background-color: #fff3cd; color: #856404; padding: 15px; border-left: 4px solid #ffeeba; margin-bottom: 20px; font-size: 13px;">
            <strong>Catatan Penting:</strong><br>
            Harap tunjukkan email ini atau invoice cetak kepada petugas kami saat melakukan daftar ulang di lokasi
            Agrowisata.
        </div>

        @if ($waLink)
            <div
                style="background-color: #f0fff4; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                <strong>Hubungi Vendor via WhatsApp</strong><br>
                @if ($vendor?->name)
                    Vendor: {{ $vendor->name }}<br>
                @endif
                @if ($vendor?->address)
                    Alamat: {{ $vendor->address }}<br>
                @endif
                @if ($vendor?->email)
                    Email: {{ $vendor->email }}<br>
                @endif
                Nomor WhatsApp: {{ $whatsappSetting->phone_number }}<br>
                <a href="{{ $waLink }}"
                    style="display: inline-block; margin-top: 10px; background-color: #25d366; color: #ffffff; padding: 10px 18px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                    Chat via WhatsApp
                </a>
            </div>
        @endif

        <div style="text-align: center; margin-top: 30px;">
            <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Ingin mencetak invoice ini untuk keperluan
                dokumentasi?</p>
            <a href="{{ route('frontend.invoice', $payment->booking->booking_code) }}"
                style="background-color: #28a745; color: white; padding: 10px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Cetak
                Invoice PDF</a>
        </div>

        <div
            style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; font-size: 12px; color: #999;">
            <p style="margin: 0;">Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} Agrowisata Tour. All rights reserved.</p>
        </div>

    </div>
</body>

</html>
