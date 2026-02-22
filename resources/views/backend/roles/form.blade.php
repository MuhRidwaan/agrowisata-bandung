@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($role) ? 'Edit Role' : 'Tambah Role' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Data Role</a></li>
                        <li class="breadcrumb-item active">{{ isset($role) ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Form {{ isset($role) ? 'Edit Role' : 'Tambah Role' }}
                            </h3>
                        </div>

                        <form action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($role))
                                @method('PUT')
                            @endif

                            <div class="card-body">

                                <div class="form-group">
                                    <label>Nama Role</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $role->name ?? '') }}" placeholder="Masukkan nama role"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <label>Hak Akses (Permissions)</label>
                                    <div class="row border rounded p-3 bg-light">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-3 col-sm-4 col-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input" type="checkbox"
                                                        id="perm_{{ $permission->id }}" name="permissions[]"
                                                        value="{{ $permission->name }}"
                                                        {{ isset($role) && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                    <label for="perm_{{ $permission->id }}"
                                                        class="custom-control-label font-weight-normal"
                                                        style="cursor: pointer;">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($role) ? 'Update' : 'Simpan' }}
                                </button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
