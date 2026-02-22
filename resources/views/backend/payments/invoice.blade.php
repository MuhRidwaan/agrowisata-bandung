@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Invoice Pembayaran</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Data Payment</a></li>
                        <li class="breadcrumb-item active">Invoice</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- MAIN INVOICE AREA -->
                    <div class="invoice p-4 mb-3" id="invoice-print-area" style="border-radius: 8px;">

                        <!-- Header Row -->
                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fas fa-leaf text-success"></i> Agrowisata Tour
                                    <small class="float-right">Tanggal Cetak: {{ now()->format('d/m/Y') }}</small>
                                </h4>
                            </div>
                        </div>
                        <hr>

                        <!-- Info Row -->
                        <div class="row invoice-info mt-4 mb-4">
                            <div class="col-sm-4 invoice-col">
                                Dari
                                <address>
                                    <strong>Admin Agrowisata</strong><br>
                                    Jl. Raya Pariwisata No. 123<br>
                                    Bandung, Jawa Barat<br>
                                    Telepon: (022) 123-4567<br>
                                    Email: info@agrowisata.com
                                </address>
                            </div>

                            <div class="col-sm-4 invoice-col">
                                Kepada Pemesan
                                <address>
                                    <strong>{{ $payment->booking->customer_name ?? $payment->booking->user->name }}</strong><br>
                                    Email: {{ $payment->booking->customer_email ?? $payment->booking->user->email }}<br>
                                    Telepon: {{ $payment->booking->customer_phone ?? '-' }}
                                </address>
                            </div>

                            <div class="col-sm-4 invoice-col">
                                <b>Invoice #{{ $payment->booking->booking_code }}</b><br>
                                <br>
                                <b>Status Pembayaran:</b> <span class="badge badge-success"
                                    style="font-size:14px;">LUNAS</span><br>
                                <b>Tgl Bayar:</b> {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}<br>
                                <b>Metode:</b> {{ strtoupper($payment->payment_method ?? 'Payment Gateway / Transfer') }}
                            </div>
                        </div>

                        <!-- Table Row -->
                        <div class="row mt-5">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Detail Paket Tour</th>
                                            <th>Jumlah Peserta</th>
                                            <th class="text-right">Harga Paket / Pax</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $payment->booking->paketTour->nama_paket }}</td>
                                            <td>{{ $payment->booking->jumlah_peserta }} Orang</td>
                                            <td class="text-right">Rp
                                                {{ number_format($payment->booking->paketTour->harga_paket, 0, ',', '.') }}
                                            </td>
                                            <td class="text-right">Rp
                                                {{ number_format($payment->booking->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Footer Row -->
                        <div class="row mt-4">
                            <div class="col-6">
                                <p class="lead">Catatan Penting:</p>
                                <img src="https://midtrans.com/assets/img/midtrans-logo.png" alt="Midtrans" width="120"
                                    class="mb-3">
                                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                    Terima kasih telah melakukan pembayaran. Harap simpan invoice ini dan tunjukkan kepada
                                    petugas kami saat melakukan registrasi ulang di lokasi Agrowisata.
                                </p>
                            </div>
                            <div class="col-6">
                                <p class="lead">Ringkasan Total</p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Subtotal:</th>
                                            <td class="text-right">Rp
                                                {{ number_format($payment->booking->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Dibayarkan:</th>
                                            <td class="text-right"><strong>Rp
                                                    {{ number_format($payment->booking->total_price, 0, ',', '.') }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi (Tidak akan ikut ter-print) -->
                        <div class="row no-print mt-5 pt-3 border-top">
                            <div class="col-12">
                                <button type="button" class="btn btn-success float-right" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Invoice
                                </button>
                                <a href="{{ route('payments.index') }}" class="btn btn-secondary float-right mr-2">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CSS Khusus agar Sidebar & Navbar ngilang pas diprint -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .main-sidebar,
            .main-header,
            .content-header {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
                background-color: white !important;
            }

            body {
                background-color: white !important;
            }
        }
    </style>
@endsection
