@extends('backend..main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <h1>Role & Permission</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card">

                <div class="card-header">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <h3 class="card-title mb-2">Data Role</h3>

                        <div class="d-flex align-items-center">

                            <!-- SEARCH -->
                            {{-- <form action="{{ route('roles.index') }}" method="GET" class="mr-2">

                                <div class="input-group input-group-sm" style="width:250px;">

                                    <input type="text" name="search" class="form-control" placeholder="Search user..."
                                        value="{{ request('search') }}">

                                    <div class="input-group-append">
                                        <button class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>

                                </div>
                            </form> --}}


                            <!-- TAMBAH -->
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah User
                            </a>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Role Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $role->name }}</td>

                                    <td>
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                            style="display:inline-block">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                    {{ $roles->links() }}

                </div>

            </div>

        </div>
    </section>
@endsection
