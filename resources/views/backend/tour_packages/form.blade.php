@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">

        <h4 class="mb-3">
            {{ isset($package) ? 'Edit Paket Tour' : 'Tambah Paket Tour' }}
        </h4>

        <div class="card">
            <div class="card-body">

                <form method="POST"
                    action="{{ isset($package) ? route('tour-packages.update', $package->id) : route('tour-packages.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if (isset($package))
                        @method('PUT')
                    @endif

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Nama Paket</label>
                            <input type="text" name="title" class="form-control"
                                value="{{ old('title', $package->title ?? '') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Lokasi</label>
                            <input type="text" name="location" class="form-control"
                                value="{{ old('location', $package->location ?? '') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Jam Mulai</label>
                            <input type="time" name="start_time" class="form-control"
                                value="{{ old('start_time', $package->start_time ?? '') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Jam Selesai</label>
                            <input type="time" name="end_time" class="form-control"
                                value="{{ old('end_time', $package->end_time ?? '') }}" required>
                        </div>

                        <!-- Field Kuota dihapus karena sudah tidak ada di tabel -->

                        <div class="col-md-4 mb-3">
                            <label>Harga Dasar</label>
                            <input type="number" name="base_price" class="form-control"
                                value="{{ old('base_price', $package->base_price ?? '') }}" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $package->description ?? '') }}</textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Thumbnail</label>
                            <input type="file" name="thumbnail" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>
                                    Aktif</option>
                                <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>
                                    Nonaktif</option>
                            </select>
                        </div>

                    </div>

                    <button class="btn btn-success">
                        Simpan
                    </button>
                    <a href="{{ route('tour-packages.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>

                </form>

            </div>
        </div>

    </div>
@endsection
