@extends('backend..main_dashboard')

@section('content')
    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        {{ isset($role) ? 'Edit Role' : 'Tambah Role' }}
                    </h3>
                </div>

                <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}" method="POST">

                    @csrf
                    @if (isset($role))
                        @method('PUT')
                    @endif

                    <div class="card-body">

                        <div class="form-group">
                            <label>Nama Role</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $role->name ?? '') }}">
                        </div>

                        <label>Permissions</label>

                        <div class="row">
                            @foreach ($permissions as $permission)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]"
                                            value="{{ $permission->name }}"
                                            {{ isset($role) && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>

                                        <label class="form-check-label">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </section>
@endsection
