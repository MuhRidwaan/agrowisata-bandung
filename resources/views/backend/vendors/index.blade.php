@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid pl-3">

        <h1 class="mb-3">Data Vendor</h1>

        <div class="mb-3">
            <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                + Tambah Vendor
            </a>
        </div>

        <!-- WRAPPER PUTIH TIPIS -->
        <div class="bg-white p-2 rounded shadow-sm">

            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Area</th>
                        <th>Deskripsi</th>
                        <th width="20%">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->email }}</td>
                            <td>{{ $vendor->phone }}</td>

                            <!-- AREA -->
                            <td>{{ $vendor->area->name ?? '-' }}</td>

                            <!-- DESKRIPSI -->
                            <td>{{ \Illuminate\Support\Str::limit($vendor->description, 50) }}</td>

                            <!-- ACTION -->
                            <td>
                                <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin mau hapus vendor ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data vendor
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

    </div>
@endsection
