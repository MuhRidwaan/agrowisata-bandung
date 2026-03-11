<!DOCTYPE html>
<html>

<head>
    <title>Invoice Pembayaran - Agrotourism Bandung</title>
</head>

<body
    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f6f9; margin: 0; padding: 20px;">

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
                    $booking = $payment->booking;
                    $paket = $booking->paketTour;
                    $baseTotal = $paket->harga_paket * $booking->jumlah_peserta;
                    $discount = $baseTotal - $booking->total_price;
                @endphp
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        {{ $paket->nama_paket ?? '-' }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                        {{ $booking->jumlah_peserta }} Pax</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp
                        {{ number_format($baseTotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                @if($discount > 0)
                <tr>
                    <td colspan="2" style="padding: 10px; border: 1px solid #ddd; text-align: right; color: #dc3545;"><strong>Potongan Diskon:</strong></td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right; color: #dc3545;"><strong>-Rp
                            {{ number_format($discount, 0, ',', '.') }}</strong></td>
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

        <div
            style="background-color: #fff3cd; color: #856404; padding: 15px; border-left: 4px solid #ffeeba; margin-bottom: 20px; font-size: 13px;">
            <strong>Catatan Penting:</strong><br>
            Harap tunjukkan email ini atau invoice cetak kepada petugas kami saat melakukan daftar ulang di lokasi
            Agrowisata.
        </div>

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
