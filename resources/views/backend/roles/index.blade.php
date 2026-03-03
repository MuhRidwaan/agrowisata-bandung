@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Role & Permission Data</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Role & Permission</li>
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
                                <h3 class="card-title mb-2">Role List</h3>
                                <div class="d-flex align-items-center">

                                    <form action="{{ route('roles.index') }}" method="GET" class="mr-2">
                                        <div class="input-group input-group-sm" style="width:250px;">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Search role..." value="{{ request('search') }}">
                                            <div class="input-group-append">
                                                <button class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    {{-- Add button hidden as roles are hardcoded --}}
                                    {{-- <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Add Role
                                    </a> --}}
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Roles are managed by the system to maintain multi-tenant logic consistency.
                            </div>

                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Role Name</th>
                                        {{-- <th width="15%">Action</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $role)
                                        <tr>
                                            {{-- Dynamic numbering for pagination --}}
                                            <td>{{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}
                                            </td>
                                            <td><strong>{{ $role->name }}</strong></td>
                                            {{-- <td>
                                                <a href="{{ route('roles.edit', $role->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                    style="display:inline-block" class="form-delete">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td> --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">Role data is empty</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $roles->links() }}
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
                title: 'Success',
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
                    title: 'Are you sure?',
                    text: "This role data will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
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
