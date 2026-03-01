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
                action="{{ isset($paketTour->id)
                    ? route('paket-tours.update', $paketTour->id)
                    : route('paket-tours.store') }}">

                @csrf
                @isset($paketTour->id)
                    @method('PUT')
                @endisset

                <div class="card-body">

                    {{-- PACKAGE NAME --}}
                    <div class="form-group">
                        <label>Package Name <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nama_paket"
                            class="form-control @error('nama_paket') is-invalid @enderror"
                            value="{{ old('nama_paket', $paketTour->nama_paket ?? '') }}"
                            required>
                        @error('nama_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="deskripsi"
                            rows="3"
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            required>{{ old('deskripsi', $paketTour->deskripsi ?? '') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PRICE --}}
                    <div class="form-group">
                        <label>Price <span class="text-danger">*</span></label>
                        <input type="number"
                            name="harga_paket"
                            step="0.01"
                            min="0"
                            class="form-control @error('harga_paket') is-invalid @enderror"
                            value="{{ old('harga_paket', $paketTour->harga_paket ?? '') }}"
                            required>
                        @error('harga_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- OPERATIONAL HOURS --}}
                    <div class="form-group">
                        <label>Operational Hours <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center">
                            <input type="time"
                                name="jam_awal"
                                class="form-control mr-2 @error('jam_awal') is-invalid @enderror"
                                value="{{ old('jam_awal', optional($paketTour->jam_awal)->format('H:i')) }}"
                                required>

                            <span class="mx-2">to</span>

                            <input type="time"
                                name="jam_akhir"
                                class="form-control ml-2 @error('jam_akhir') is-invalid @enderror"
                                value="{{ old('jam_akhir', optional($paketTour->jam_akhir)->format('H:i')) }}"
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
                        <label>Vendor <span class="text-danger">*</span></label>
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

                    {{-- ACTIVITIES --}}
                    <div class="form-group">
                        <label>Activities <span class="text-danger">*</span></label>

                        <div id="activity-wrapper">

                            @if(old('aktivitas'))
                                @foreach(old('aktivitas') as $item)
                                    <div class="input-group mb-2">
                                        <input type="text"
                                            name="aktivitas[]"
                                            class="form-control"
                                            value="{{ $item }}">
                                        <div class="input-group-append">
                                            <button type="button"
                                                class="btn btn-danger"
                                                onclick="removeActivity(this)">-</button>
                                        </div>
                                    </div>
                                @endforeach

                            @elseif(isset($paketTour->aktivitas) && is_array($paketTour->aktivitas))
                                @foreach($paketTour->aktivitas as $item)
                                    <div class="input-group mb-2">
                                        <input type="text"
                                            name="aktivitas[]"
                                            class="form-control"
                                            value="{{ $item }}">
                                        <div class="input-group-append">
                                            <button type="button"
                                                class="btn btn-danger"
                                                onclick="removeActivity(this)">-</button>
                                        </div>
                                    </div>
                                @endforeach

                            @else
                                <div class="input-group mb-2">
                                    <input type="text"
                                        name="aktivitas[]"
                                        class="form-control">
                                    <div class="input-group-append">
                                        <button type="button"
                                            class="btn btn-success"
                                            onclick="addActivity()">+</button>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <button type="button"
                            class="btn btn-sm btn-secondary mt-2"
                            onclick="addActivity()">
                            + Add Activity
                        </button>

                        @error('aktivitas')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($paketTour->id) ? 'Save' : 'Save' }}
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

@section('scripts')
<script>
function addActivity() {
    const wrapper = document.getElementById('activity-wrapper');

    const div = document.createElement('div');
    div.classList.add('input-group', 'mb-2');

    div.innerHTML = `
        <input type="text" name="aktivitas[]" class="form-control">
        <div class="input-group-append">
            <button type="button" class="btn btn-danger" onclick="removeActivity(this)">-</button>
        </div>
    `;

    wrapper.appendChild(div);
}

function removeActivity(button) {
    button.closest('.input-group').remove();
}
</script>
@endsection