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
                                <h3 class="card-title mb-2">Daftar Booking Masuk</h3>
                                <div class="d-flex align-items-center">

                                    <!-- Search Form -->
                                    <form action="{{ route('bookings.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width:250px;">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari kode/user..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default"><i
                                                        class="fas fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>

                                    <a href="{{ route('bookings.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Booking Baru
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Kode Booking</th>
                                        <th>Nama Pemesan</th>
                                        <th>Paket Tour</th>
                                        <th class="text-center">Peserta</th>
                                        <th>Total Harga</th>
                                        <th class="text-center">Status</th>
                                        <th width="12%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bookings as $booking)
                                        <tr>
                                            <td class="text-center">
                                                {{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}
                                            </td>
                                            <td><strong>{{ $booking->booking_code }}</strong></td>
                                            <td>
                                                {{ $booking->customer_name ?? ($booking->user->name ?? 'User Dihapus') }} <br>
                                                <small class="text-muted">{{ $booking->customer_phone ?? '' }}</small>
                                            </td>
                                            <td>{{ $booking->paketTour->nama_paket ?? '-' }}</td>
                                            <td class="text-center">{{ $booking->jumlah_peserta }} Pax</td>
                                            <td>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                @if ($booking->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($booking->status == 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif($booking->status == 'confirmed')
                                                    <span class="badge badge-info">Confirmed</span>
                                                @else
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('bookings.edit', $booking->id) }}"
                                                    class="btn btn-warning btn-sm" title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST"
                                                    style="display:inline-block" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus Data">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-2"></i><br>
                                                Belum ada data booking.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-4">
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
                    title: 'Yakin mau dihapus?',
                    text: "Data booking ini beserta data pembayarannya akan dihapus permanen!",
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
