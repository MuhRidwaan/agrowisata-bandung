@extends('backend.backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Data Pengguna</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Data Pengguna</li>
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

                                <h3 class="card-title mb-2">Data Pengguna</h3>

                                <div class="d-flex align-items-center">

                                    <!-- SEARCH -->
                                    <form action="{{ route('users.index') }}" method="GET" class="mr-2">

                                        <div class="input-group input-group-sm" style="width:250px;">

                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search user..." value="{{ request('search') }}">

                                            <div class="input-group-append">
                                                <button class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>

                                        </div>
                                    </form>



                                    <!-- EXPORT -->
                                    <a href="{{ route('users.export') }}" class="btn btn-success btn-sm mr-1">
                                        <i class="fas fa-file-export"></i> Export
                                    </a>

                                    <!-- TAMBAH -->
                                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah User
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
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($users as $key => $user)
                                        <tr>
                                            <td>
                                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                            </td>

                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>

                                            <td>

                                                <!-- EDIT -->
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <!-- DELETE -->
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
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
                                            <td colspan="4" class="text-center">
                                                Data kosong
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>
                            <div class="mt-3">
                                {{ $users->links() }}
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
