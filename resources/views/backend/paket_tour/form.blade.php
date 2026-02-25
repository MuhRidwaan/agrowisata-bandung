@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ isset($paketTour->id) ? 'Edit Tour Package' : 'Add Tour Package' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('paket-tours.index') }}">Tour Package Data</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($paketTour->id) ? 'Edit' : 'Add' }} Tour Package
                    </li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">

            <div class="card-header">
                <h3 class="card-title">
                    {{ isset($paketTour->id) ? 'Edit Tour Package Form' : 'Add Tour Package Form' }}
                </h3>
            </div>

            <form method="POST"
                action="{{ isset($paketTour->id) ? route('paket-tours.update', $paketTour->id) : route('paket-tours.store') }}">
                
                @csrf
                @if (isset($paketTour->id))
                    @method('PUT')
                @endif

                <div class="card-body">

                    {{-- PACKAGE NAME --}}
                    <div class="form-group">
                        <label for="nama_paket">
                            Package Name <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control @error('nama_paket') is-invalid @enderror"
                            id="nama_paket"
                            name="nama_paket"
                            value="{{ old('nama_paket', $paketTour->nama_paket ?? '') }}"
                            required>
                        @error('nama_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="form-group">
                        <label for="deskripsi">
                            Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                            id="deskripsi"
                            name="deskripsi"
                            rows="3"
                            required>{{ old('deskripsi', $paketTour->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PRICE --}}
                    <div class="form-group">
                        <label for="harga_paket">
                            Price <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('harga_paket') is-invalid @enderror"
                            id="harga_paket"
                            name="harga_paket"
                            step="0.01"
                            min="0"
                            value="{{ old('harga_paket', $paketTour->harga_paket ?? '') }}"
                            required>
                        @error('harga_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- OPERATIONAL HOURS --}}
                    <div class="form-group">
                        <label>
                            Operational Hours <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex align-items-center">
                            <input type="time"
                                class="form-control mr-2 @error('jam_awal') is-invalid @enderror"
                                name="jam_awal"
                                value="{{ old('jam_awal', $paketTour->jam_awal ?? '') }}"
                                required>

                            <span class="mx-2">to</span>

                            <input type="time"
                                class="form-control ml-2 @error('jam_akhir') is-invalid @enderror"
                                name="jam_akhir"
                                value="{{ old('jam_akhir', $paketTour->jam_akhir ?? '') }}"
                                required>
                        </div>
                        @error('jam_awal')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('jam_akhir')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- VENDOR --}}
                    <div class="form-group">
                        <label for="vendor_id">
                            Vendor <span class="text-danger">*</span>
                        </label>
                        <select name="vendor_id"
                            class="form-control @error('vendor_id') is-invalid @enderror"
                            required>
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('vendor_id', $paketTour->vendor_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($paketTour->id) ? 'Update' : 'Save' }}
                    </button>
                    <a href="{{ route('paket-tours.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

            </form>

        </div>
    </div>
</section>

@endsection