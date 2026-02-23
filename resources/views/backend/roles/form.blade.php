@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($role) ? 'Edit Role' : 'Add Role' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role Data</a></li>
                        <li class="breadcrumb-item active">{{ isset($role) ? 'Edit' : 'Add' }}</li>
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
                                {{ isset($role) ? 'Edit Role Form' : 'Add Role Form' }}
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
                                    <label>Role Name</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $role->name ?? '') }}" placeholder="Enter role name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mt-4">
                                    <label>Permissions</label>
                                    <div class="border rounded p-3 bg-light">
                                        @foreach ($permissions as $module => $permissionList)
                                            <div class="row mb-3 border-bottom pb-2">
                                                <div class="col-12">
                                                    <h6 class="font-weight-bold text-primary">{{ ucfirst($module) }} Management</h6>
                                                </div>
                                                @foreach ($permissionList as $permission)
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
                                        @endforeach
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($role) ? 'Update' : 'Save' }}
                                </button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                    Back
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
