@extends('backend..main_dashboard')

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
                        <li class="breadcrumb-item active">Tour Package Data</li>
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
                                <h3 class="card-title mb-2">Tour Package Data</h3>
                                <a href="{{ route('paket-tours.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Tour Package
                                </a>
                            </div>
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
                                            <td>{{ $paket->jam_awal ?? '-' }} to {{ $paket->jam_akhir ?? '-' }}</td>
                                            <td>{{ $paket->vendor->name ?? '-' }}</td>
                                            <td>
                                                @if ($paket->tanggalAvailables && $paket->tanggalAvailables->count())
                                                    @foreach ($paket->tanggalAvailables as $tgl)
                                                        <span class="badge badge-info mb-1">{{ $tgl->tanggal }} (Quota:
                                                            {{ $tgl->kuota }})</span><br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            <td>

                                                <!-- EDIT -->
                                                <a href="{{ route('paket-tours.edit', $paket->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- DELETE -->
                                                <form action="{{ route('paket-tours.destroy', $paket->id) }}" method="POST" 
                                                    style="display:inline-block" class="form-delete">

                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">
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
            </div>
        </div>
    </section>
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

    <script>
        document.querySelector('.form-delete').addEventListener('submit', function(e) {
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
                    this.submit();
                }
            });
        });
    </script>
@endsection