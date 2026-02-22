@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Payment</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Payment</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <!-- HEADER -->
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="card-title mb-2">Daftar Pembayaran Masuk</h3>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('payments.index') }}" method="GET">
                                        <div class="input-group input-group-sm" style="width:250px;">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari kode/user..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default"><i
                                                        class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- BODY -->
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Kode Booking</th>
                                        <th>Nama Pemesan</th>
                                        <th>Total Harga</th>
                                        <th class="text-center">Status</th>
                                        <th width="22%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                                            </td>
                                            <td><strong>{{ $payment->booking->booking_code ?? '-' }}</strong></td>
                                            <td>
                                                {{ $payment->booking->customer_name ?? ($payment->booking->user->name ?? '-') }}
                                                <br>
                                                <small
                                                    class="text-muted">{{ $payment->booking->customer_phone ?? '' }}</small>
                                            </td>
                                            <td>Rp {{ number_format($payment->booking->total_price ?? 0, 0, ',', '.') }}
                                            </td>

                                            <!-- UPDATE STATUS BADGE -->
                                            <td class="text-center">
                                                @if ($payment->status == 'success')
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i>
                                                        Paid / Lunas</span>
                                                @elseif ($payment->status == 'failed')
                                                    <span class="badge badge-danger"><i class="fas fa-times-circle"></i>
                                                        Expired / Failed</span>
                                                @else
                                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Waiting
                                                        Payment</span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                @if ($payment->status == 'pending')
                                                    <!-- TOMBOL BAYAR MIDTRANS -->
                                                    <button class="btn btn-primary btn-sm btn-pay mb-1"
                                                        data-token="{{ $payment->snap_token }}"
                                                        data-url="{{ route('payments.paid', $payment->id) }}">
                                                        <i class="fas fa-money-bill-wave"></i> Bayar
                                                    </button>

                                                    <!-- TOMBOL BATALKAN MANUAL -->
                                                    <form action="{{ route('payments.cancel', $payment->id) }}"
                                                        method="POST" style="display:inline-block" class="form-cancel">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm mb-1"
                                                            title="Batalkan jika Expired">
                                                            <i class="fas fa-times"></i> Batal
                                                        </button>
                                                    </form>
                                                @elseif ($payment->status == 'success')
                                                    <span class="text-success font-weight-bold mr-2"><i
                                                            class="fas fa-check"></i> Lunas</span>
                                                    <!-- TOMBOL CETAK INVOICE -->
                                                    <a href="{{ route('payments.invoice', $payment->id) }}"
                                                        class="btn btn-info btn-sm">
                                                        <i class="fas fa-print"></i> Invoice
                                                    </a>
                                                @elseif ($payment->status == 'failed')
                                                    <span class="text-danger font-weight-bold"><i class="fas fa-ban"></i>
                                                        Dibatalkan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-money-check-alt fa-3x text-muted mb-2"></i><br>
                                                Belum ada data pembayaran.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- PAGINATION -->
                            <div class="mt-4">
                                {{ $payments->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LOAD SCRIPT MIDTRANS SANDBOX -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <!-- LOGIC SWEETALERT & MUNCULIN POP-UP MIDTRANS -->
    <script>
        document.querySelectorAll('.btn-pay').forEach(button => {
            button.addEventListener('click', function() {
                var snapToken = this.getAttribute('data-token');
                var paymentUrl = this.getAttribute('data-url');

                if (!snapToken) {
                    Swal.fire('Error', 'Token pembayaran tidak ditemukan!', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Lanjutkan Pembayaran?',
                    text: "Anda akan diarahkan ke halaman aman Midtrans untuk membayar.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Bayar Sekarang!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.snap.pay(snapToken, {
                            onSuccess: function(result) {
                                Swal.fire('Berhasil!',
                                    'Pembayaran sukses! Memproses data...',
                                    'success');
                                var form = document.createElement('form');
                                form.method = 'POST';
                                form.action = paymentUrl;
                                var csrf = document.createElement('input');
                                csrf.type = 'hidden';
                                csrf.name = '_token';
                                csrf.value = '{{ csrf_token() }}';
                                form.appendChild(csrf);
                                document.body.appendChild(form);
                                form.submit();
                            },
                            onPending: function(result) {
                                Swal.fire('Menunggu',
                                    'Silakan selesaikan pembayaran Anda.', 'info');
                            },
                            onError: function(result) {
                                Swal.fire('Gagal',
                                    'Pembayaran gagal, silakan coba lagi.', 'error');
                            },
                            onClose: function() {
                                Swal.fire('Ditutup',
                                    'Anda menutup layar sebelum menyelesaikannya.',
                                    'warning');
                            }
                        });
                    }
                });
            });
        });

        // SCRIPT UNTUK KONFIRMASI TOMBOL BATAL MANUAL
        document.querySelectorAll('.form-cancel').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Batalkan Pembayaran?',
                    text: "Jika transaksi di Midtrans sudah Expired, klik Ya untuk membatalkan pesanan ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
