@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Booking</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Booking</li>
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
                                <h3 class="card-title mb-2">Daftar Booking</h3>
                                <div class="d-flex align-items-center">
                                    <form action="{{ route('bookings.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width:250px;">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari kode/user..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-default"><i class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>

                                    <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Booking
                                    </a>
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
                                        <th>Peserta</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th width="12%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookings as $booking)
                                        <tr>
                                            <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}
                                            </td>
                                            <td><strong>{{ $booking->booking_code }}</strong></td>
                                            <td>{{ $booking->user->name ?? 'User Dihapus' }}</td>
                                            <td>{{ $booking->paketTour->nama_paket ?? '-' }}</td>
                                            <td>{{ $booking->jumlah_peserta }} Orang</td>
                                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($booking->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge badge-success">Confirmed</span>
                                                @else
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('bookings.edit', $booking->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST"
                                                    style="display:inline-block" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Data booking kosong</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $bookings->links() }}
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
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Yakin?',
                    text: "Data booking ini akan dihapus permanen",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
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
