<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran - {{ $payment->booking->booking_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .invoice-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            border-bottom: 2px solid #198754;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        /* Hilangkan tombol saat di-print */
        @media print {
            body {
                background-color: #fff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="invoice-container">

            <div class="d-flex justify-content-between mb-4 no-print">
                <a href="{{ url('/') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i>
                    Kembali ke Web</a>
                <button onclick="window.print()" class="btn btn-success"><i class="fas fa-print"></i> Cetak PDF</button>
            </div>

            <div class="row invoice-header">
                <div class="col-sm-6">
                    <h2 class="text-success fw-bold"><i class="fas fa-leaf"></i> Agrotourism Bandung</h2>
                    <p class="text-muted mb-0">Invoice Resmi Pembayaran</p>
                </div>
                <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                    <h5 class="fw-bold mb-1">Invoice #{{ $payment->booking->booking_code }}</h5>
                    <p class="text-muted mb-0">Dicetak: {{ now()->format('d M Y') }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <h6 class="text-muted text-uppercase fw-bold">Dibayar Oleh:</h6>
                    <p class="mb-1 fw-bold fs-5">{{ $payment->booking->customer_name ?? $payment->booking->user->name }}
                    </p>
                    <p class="mb-0 text-muted">{{ $payment->booking->customer_phone ?? '-' }}</p>
                    <p class="mb-0 text-muted">{{ $payment->booking->customer_email ?? $payment->booking->user->email }}
                    </p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="text-muted text-uppercase fw-bold">Status Pembayaran:</h6>
                    @if ($payment->status == 'success')
                        <span class="badge bg-success fs-6 px-3 py-2"><i class="fas fa-check-circle"></i> LUNAS</span>
                        <p class="mt-2 mb-0 small text-muted">Dibayar pada:
                            <br>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}</p>
                    @elseif($payment->status == 'pending')
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="fas fa-clock"></i> MENUNGGU
                            PEMBAYARAN</span>
                    @else
                        <span class="badge bg-danger fs-6 px-3 py-2"><i class="fas fa-times-circle"></i> KADALUARSA /
                            BATAL</span>
                    @endif
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Deskripsi Paket Tour</th>
                            <th class="text-center">Peserta</th>
                            <th class="text-end">Harga Paket</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $booking = $payment->booking;
                            $paket = $booking->paketTour;
                            $baseTotal = ($paket->harga_paket ?? 0) * $booking->jumlah_peserta;
                            $discount = $baseTotal - $booking->total_price;
                        @endphp
                        <tr>
                            <td>{{ $paket->nama_paket ?? '-' }}</td>
                            <td class="text-center">{{ $booking->jumlah_peserta }} Pax</td>
                            <td class="text-end">Rp {{ number_format($paket->harga_paket ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($baseTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="alert alert-warning mb-0" style="font-size: 0.9rem;">
                        <strong>Catatan:</strong><br>
                        Harap simpan bukti ini dan tunjukkan kepada petugas loket saat daftar ulang di lokasi
                        Agrowisata.
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-bold">Rp {{ number_format($baseTotal, 0, ',', '.') }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-danger">Diskon:</span>
                        <span class="text-danger fw-bold">-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="text-muted mb-0">Total Bayar:</h5>
                        <h3 class="text-success fw-bold mb-0">Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
