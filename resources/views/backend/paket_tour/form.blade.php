@extends('backend.backend.main_dashboard')

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
                                    <label for="jam_operasional">Jam Operasional</label>
                                    <input type="time" class="form-control" id="jam_operasional" name="jam_operasional"
                                        value="{{ old('jam_operasional', $paketTour->jam_operasional) }}">
                                </div>
                                <div class="form-group">
                                    <label for="harga_paket">Harga Paket</label>
                                    <input type="number" class="form-control" id="harga_paket" name="harga_paket"
                                        value="{{ old('harga_paket', $paketTour->harga_paket) }}" min="0"
                                        step="1000" required placeholder="Contoh: 100000">
                                </div>
                                <div class="form-group">
                                    <label for="kuota">Kuota / Limit Booking</label>
                                    <input type="number" class="form-control" id="kuota" name="kuota"
                                        value="{{ old('kuota', $paketTour->kuota) }}" min="1" required
                                        placeholder="Minimal 1">
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
