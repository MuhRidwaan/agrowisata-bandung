@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Data Vendor</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Data Vendor</li>
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

                                <h3 class="card-title mb-2">Data Vendor</h3>

                                <div class="d-flex align-items-center">

                                    <!-- TAMBAH -->
                                    <a href="{{ route('vendors.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Vendor
                                    </a>

                                </div>

                            </div>

                        </div>
                        <!-- END HEADER -->


                        <div class="card-body">

                            <table class="table table-bordered table-hover">

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

                                    @forelse ($vendors as $key => $vendor)
                                        <tr>
                                            <td>
                                                {{ ($vendors->currentPage() - 1) * $vendors->perPage() + $loop->iteration }}
                                            </td>

                                            <td>{{ $vendor->name }}</td>
                                            <td>{{ $vendor->email }}</td>

                                            <!-- PHONE + FORMAT -->
                                            <td>
                                                {{ \Illuminate\Support\Str::startsWith($vendor->phone, '+') 
                                                    ? $vendor->phone 
                                                    : '+' . $vendor->phone }}
                                            </td>

                                            <!-- AREA -->
                                            <td>{{ $vendor->area->name ?? '-' }}</td>

                                            <!-- DESKRIPSI -->
                                            <td>{{ \Illuminate\Support\Str::limit($vendor->description, 50) }}</td>

                                            <td>

                                                <!-- EDIT -->
                                                <a href="{{ route('vendors.edit', $vendor->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- DELETE -->
                                                <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST"
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
                                            <td colspan="7" class="text-center">
                                                Data vendor kosong
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                            <!-- PAGINATION -->
                            <div class="mt-3">
                                {{ $vendors->links() }}
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- ALERT SUCCESS --}}
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

    {{-- DELETE CONFIRM --}}
    <script>
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Yakin?',
                    text: "Data akan dihapus permanen",
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
