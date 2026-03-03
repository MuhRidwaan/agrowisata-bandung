@extends('backend..main_dashboard')

@section('content')
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>{{ isset($user) ? 'Edit User' : 'Add User' }}</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('users.index') }}">User Data</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($user) ? 'Edit' : 'Add' }}
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </section>


    <!-- CONTENT -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">

                    <div class="card card-primary">

                        <div class="card-header">
                            <h3 class="card-title">
                                {{ isset($user) ? 'Edit User Form' : 'Add User Form' }}
                            </h3>
                        </div>

                        <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}"
                            method="POST">

                            @csrf
                            @if (isset($user))
                                @method('PUT')
                            @endif

                            <div class="card-body">

                                <!-- NAME -->
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name ?? '') }}" placeholder="Enter name">
                                </div>

                                <!-- EMAIL -->
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email ?? '') }}" placeholder="Enter email">
                                </div>

                                <!-- PASSWORD -->
                                <div class="form-group">
                                    <label>
                                        Password
                                        @if (isset($user))
                                            <small class="text-muted">(leave blank if not changing)</small>
                                        @endif
                                    </label>

                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>

                                <div class="form-group">
                                    <label>Role</label>

                                    <select name="role" id="role-select" class="form-control">

                                        <option value="">-- Select role --</option>

                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <!-- VENDOR ASSOCIATION (Only for Vendor Role) -->
                                <div class="form-group" id="vendor-group" style="display: none;">
                                    <label>Assign to Vendor</label>
                                    <select name="vendor_id" class="form-control">
                                        <option value="">-- Select Vendor --</option>
                                        @foreach ($vendors as $v)
                                            <option value="{{ $v->id }}"
                                                {{ (isset($user) && $user->vendor && $user->vendor->id == $v->id) ? 'selected' : '' }}>
                                                {{ $v->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Required if role is Vendor to enable multi-tenancy.</small>
                                </div>


                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary">
                                    {{ isset($user) ? 'Update' : 'Save' }}
                                </button>

                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    Back
                                </a>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role-select');
            const vendorGroup = document.getElementById('vendor-group');

            function toggleVendor() {
                if (roleSelect.value === 'Vendor') {
                    vendorGroup.style.display = 'block';
                } else {
                    vendorGroup.style.display = 'none';
                }
            }

            roleSelect.addEventListener('change', toggleVendor);
            toggleVendor(); // Run on load
        });
    </script>
@endsection
