@extends('backend..main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($paketTour->id) ? 'Edit Paket Tour' : 'Tambah Paket Tour' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('paket-tours.index') }}">Data Paket Tour</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($paketTour->id) ? 'Edit' : 'Tambah' }} Paket Tour
                        </li>
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
                                {{ isset($paketTour->id) ? 'Form Edit Paket Tour' : 'Form Tambah Paket Tour' }}
                            </h3>
                        </div>
                        <form method="POST"
                            action="{{ isset($paketTour->id) ? route('paket-tours.update', $paketTour->id) : route('paket-tours.store') }}">
                            @csrf
                            @if (isset($paketTour->id))
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nama_paket">Nama Paket</label>
                                    <input type="text" class="form-control" id="nama_paket" name="nama_paket"
                                        value="{{ old('nama_paket', $paketTour->nama_paket) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $paketTour->deskripsi) }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Jam Operasional</label>
                                    <div class="d-flex">
                                        <input type="time" class="form-control mr-2" name="jam_awal" id="jam_awal" value="{{ old('jam_awal', $paketTour->jam_awal ?? '') }}">
                                        <span class="mx-2">s/d</span>
                                        <input type="time" class="form-control ml-2" name="jam_akhir" id="jam_akhir" value="{{ old('jam_akhir', $paketTour->jam_akhir ?? '') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="vendor_id">Vendor</label>
                                    <select name="vendor_id" class="form-control" required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach($vendors as $id => $name)
                                            <option value="{{ $id }}" {{ old('vendor_id', $paketTour->vendor_id ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($paketTour->id) ? 'Update' : 'Simpan' }}</button>
                                <a href="{{ route('paket-tours.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
