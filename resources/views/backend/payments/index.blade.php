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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <h3 class="card-title mb-2">Daftar Pembayaran</h3>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('payments.index') }}" method="GET">
                                        <div class="input-group input-group-sm" style="width:250px;">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari kode booking/user..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode Booking</th>
                                        <th>User</th>
                                        <th>Paket Tour</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Waktu Bayar</th>
                                        <th width="12%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                                            </td>
                                            <td><strong>{{ $payment->booking->booking_code ?? '-' }}</strong></td>
                                            <td>{{ $payment->booking->user->name ?? 'User Dihapus' }}</td>
                                            <td>{{ $payment->booking->paketTour->nama_paket ?? '-' }}</td>
                                            <td>Rp {{ number_format($payment->booking->total_price ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if ($payment->status == 'success')
                                                    <span class="badge badge-success">Paid</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y H:i') : '-' }}
                                            </td>
                                            <td>
                                                @if ($payment->status != 'success')
                                                    <form method="POST" action="{{ route('payments.paid', $payment->id) }}"
                                                        class="form-paid" style="display:inline-block">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="fas fa-check-circle"></i> Mark Paid
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted"><i class="fas fa-check"></i> Selesai</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Data payment kosong</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        document.querySelectorAll('.form-paid').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Pembayaran?',
                    text: "Status payment dan booking akan diubah menjadi lunas!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Lunas!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
