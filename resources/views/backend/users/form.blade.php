@extends('backend.backend.main_dashboard')

@section('content')
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>{{ isset($user) ? 'Edit Pengguna' : 'Tambah Pengguna' }}</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('users.index') }}">Data Pengguna</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($user) ? 'Edit' : 'Tambah' }}
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
                                {{ isset($user) ? 'Form Edit User' : 'Form Tambah User' }}
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
                                    <label>Nama</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name ?? '') }}" placeholder="Masukkan nama">
                                </div>

                                <!-- EMAIL -->
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $user->email ?? '') }}" placeholder="Masukkan email">
                                </div>

                                <!-- PASSWORD -->
                                <div class="form-group">
                                    <label>
                                        Password
                                        @if (isset($user))
                                            <small class="text-muted">(kosongkan jika tidak diubah)</small>
                                        @endif
                                    </label>

                                    <input type="password" name="password" class="form-control" placeholder="Password">
                                </div>

                                <div class="form-group">
                                    <label>Role</label>

                                    <select name="role" class="form-control">

                                        <option value="">-- pilih role --</option>

                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>


                            </div>

                            <div class="card-footer">
                                <button class="btn btn-primary">
                                    {{ isset($user) ? 'Update' : 'Simpan' }}
                                </button>

                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
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
