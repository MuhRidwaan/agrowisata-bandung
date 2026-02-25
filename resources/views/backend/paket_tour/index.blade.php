@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tour Package Data</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Tour Package Data
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">
                    Tour Package Data
                </h3>

                <a href="{{ route('paket-tours.create') }}"
                   class="btn btn-primary btn-sm ml-auto">
                    <i class="fas fa-plus"></i>
                    Add Tour Package
                </a>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Package Name</th>
                            <th>Description</th>
                            <th>Operational Hours</th>
                            <th>Vendor</th>
                            <th class="text-right">Price</th>
                            <th>Available Dates</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paketTours as $key => $paket)
                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>{{ $paket->nama_paket }}</td>

                                <td>{{ $paket->deskripsi }}</td>

                                <td>
                                    {{ $paket->jam_awal ?? '-' }}
                                    to
                                    {{ $paket->jam_akhir ?? '-' }}
                                </td>

                                <td>
                                    {{ $paket->vendor->name ?? '-' }}
                                </td>

                                {{-- PRICE --}}
                                <td class="text-right">
                                    @if ($paket->harga_paket)
                                        Rp {{ number_format($paket->harga_paket, 0, ',', '.') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- AVAILABLE DATES --}}
                                <td>
                                    @if ($paket->tanggalAvailables && $paket->tanggalAvailables->count())
                                        @foreach ($paket->tanggalAvailables as $tgl)
                                            <span class="badge badge-info mb-1">
                                                {{ $tgl->tanggal }}
                                                (Quota: {{ $tgl->kuota }})
                                            </span>
                                            <br>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td>
                                    <a href="{{ route('paket-tours.edit', $paket->id) }}"
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('paket-tours.destroy', $paket->id) }}"
                                          method="POST"
                                          style="display:inline-block"
                                          class="form-delete">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    No tour package data available yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>

{{-- SUCCESS ALERT --}}
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif

{{-- DELETE CONFIRMATION (FIXED MULTIPLE FORMS) --}}
<script>
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete?',
                text: "Deleted data cannot be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

@endsection