@extends('backend..main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($photo->id) ? 'Edit Foto' : 'Tambah Foto' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('paket-tour-photos.index') }}">Gallery Foto</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($photo->id) ? 'Edit' : 'Tambah' }} Foto
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
                                {{ isset($photo->id) ? 'Form Edit Foto' : 'Form Tambah Foto' }}
                            </h3>
                        </div>
                        <form method="POST"
                            action="{{ isset($photo->id) ? route('paket-tour-photos.update', $photo->id) : route('paket-tour-photos.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($photo->id))
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="paket_tour_id">Paket Tour</label>
                                    <select name="paket_tour_id" id="paket_tour_id" class="form-control" required>
                                        <option value="">-- Pilih Paket Tour --</option>
                                        @foreach ($paketTours as $paket)
                                            <option value="{{ $paket->id }}"
                                                {{ old('paket_tour_id', $photo->paket_tour_id) == $paket->id ? 'selected' : '' }}>
                                                {{ $paket->nama_paket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="path_foto">Upload Foto</label>
                                    <input type="file" class="form-control" id="path_foto" name="path_foto"
                                        accept="image/*" {{ isset($photo->id) ? '' : 'required' }}>
                                    @if (isset($photo->path_foto) && $photo->path_foto)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $photo->path_foto) }}" alt="Foto"
                                                style="max-width:120px;max-height:120px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($photo->id) ? 'Update' : 'Simpan' }}</button>
                                <a href="{{ route('paket-tour-photos.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
