@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Payment Invoice</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payments.index') }}">Payment Data</a></li>
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
                    <div class="invoice p-4 mb-3" id="invoice-print-area" style="border-radius: 8px;">

                        <div class="row">
                            <div class="col-12">
                                <h4>
                                    <i class="fas fa-leaf text-success"></i> Agrowisata Tour
                                    <small class="float-right">Print Date: {{ now()->format('d/m/Y') }}</small>
                                </h4>
                            </div>
                        </div>
                        <hr>

                        <div class="row invoice-info mt-4 mb-4">
                            <div class="col-sm-4 invoice-col">
                                From
                                <address>
                                    <strong>Agrowisata Admin</strong><br>
                                    Jl. Raya Pariwisata No. 123<br>
                                    Bandung, West Java<br>
                                    Phone: (022) 123-4567<br>
                                    Email: info@agrowisata.com
                                </address>
                            </div>

                            <div class="col-sm-4 invoice-col">
                                To Customer
                                <address>
                                    <strong>{{ $payment->booking->customer_name ?? $payment->booking->user->name }}</strong><br>
                                    Email: {{ $payment->booking->customer_email ?? $payment->booking->user->email }}<br>
                                    Phone: {{ $payment->booking->customer_phone ?? '-' }}
                                </address>
                            </div>

                            <div class="col-sm-4 invoice-col">
                                <b>Invoice #{{ $payment->booking->booking_code }}</b><br>
                                <br>
                                <b>Payment Status:</b> <span class="badge badge-success"
                                    style="font-size:14px;">PAID</span><br>
                                <b>Payment Date:</b>
                                {{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}<br>
                                <b>Method:</b> {{ strtoupper($payment->payment_method ?? 'Payment Gateway / Transfer') }}
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Tour Package Details</th>
                                            <th class="text-center">Pax</th>
                                            <th class="text-right">Price / Pax</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $booking = $payment->booking;
                                            $paket = $booking->paketTour;
                                            $pax = (int) ($booking->jumlah_peserta ?? 0);
                                            $hargaNormal = (float) ($paket->harga_paket ?? 0);
                                            $baseTotal = $hargaNormal * $pax; // harga normal sebelum diskon/bundling

                                            $umkmItems = $booking->umkmProducts ?? collect();
                                            $umkmTotal = $umkmItems->sum(function ($product) {
                                                $qty = (int) ($product->pivot->quantity ?? 0);
                                                $price = (float) ($product->pivot->price ?? $product->price ?? 0);
                                                return $price * $qty;
                                            });

                                            // Harga tour yang benar-benar dibayar (total_price sudah termasuk UMKM)
                                            $tourPaid = max(0, (float) ($booking->total_price ?? 0) - $umkmTotal);
                                            // Diskon = selisih harga normal vs harga tour yang dibayar
                                            $discount = max(0, $baseTotal - $tourPaid);
                                            // Harga per pax efektif (setelah diskon/bundling)
                                            $pricePerPax = $pax > 0 ? $tourPaid / $pax : $hargaNormal;
                                        @endphp
                                        <tr>
                                            <td>{{ $paket->nama_paket }}</td>
                                            <td class="text-center">{{ $pax }}</td>
                                            <td class="text-right">Rp {{ number_format($pricePerPax, 0, ',', '.') }}</td>
                                            <td class="text-right">Rp {{ number_format($tourPaid, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if (($umkmItems ?? collect())->isNotEmpty())
                            <div class="row mt-3">
                                <div class="col-12 table-responsive">
                                    <table class="table table-striped">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Produk UMKM (Tambahan)</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-right">Harga</th>
                                                <th class="text-right">Total</th>
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
                                                    <td>{{ $product->name ?? '-' }}</td>
                                                    <td class="text-center">{{ $qty }}</td>
                                                    <td class="text-right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                                                    <td class="text-right">Rp {{ number_format($lineTotal, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-6">
                                <p class="lead">Important Notes:</p>
                                <img src="https://midtrans.com/assets/img/midtrans-logo.png" alt="Midtrans" width="120"
                                    class="mb-3">
                                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                    Thank you for your payment. Please keep this invoice and show it to our staff during
                                    re-registration at the Agrowisata location.
                                </p>
                            </div>
                            <div class="col-6">
                                <p class="lead">Total Summary</p>
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:50%">Subtotal Tour:</th>
                                            <td class="text-right">Rp {{ number_format($baseTotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @if ($discount > 0)
                                            <tr>
                                                <th class="text-danger">Discount:</th>
                                                <td class="text-right text-danger">-Rp {{ number_format($discount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        @if (($umkmTotal ?? 0) > 0)
                                            <tr>
                                                <th>UMKM Add-on:</th>
                                                <td class="text-right">Rp {{ number_format($umkmTotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Total Paid:</th>
                                            <td class="text-right text-success">
                                                <h4 class="mb-0 font-weight-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</h4>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row no-print mt-5 pt-3 border-top">
                            <div class="col-12">
                                <button type="button" class="btn btn-success float-right" onclick="window.print()">
                                    <i class="fas fa-print"></i> Print Invoice
                                </button>

                                <form action="{{ route('payments.send_email', $payment->id) }}" method="POST"
                                    class="float-right mr-2">
                                    @csrf
                                    <button type="submit" class="btn btn-warning text-white"
                                        title="Kirim Ulang Email Invoice">
                                        <i class="fas fa-envelope"></i> Send Email
                                    </button>
                                </form>

                                <a href="{{ route('payments.index') }}" class="btn btn-secondary float-right mr-2">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
