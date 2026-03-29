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
                enctype="multipart/form-data"
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

                    {{-- BUNDLING PACKAGES --}}
                    <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="mb-0">Bundling Packages</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBundlingRow()">
                                + Add Bundling
                            </button>
                        </div>
                        <small class="form-text text-muted mb-3">
                            Tambahkan opsi bundling dengan jumlah orang dan harga paket khusus. Kosongkan semua baris jika paket ini tidak memiliki bundling.
                        </small>

                        <div id="bundling-wrapper">
                            @foreach(($bundlings ?? collect()) as $index => $bundling)
                                <div class="border rounded p-3 mb-3 bundling-row">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <strong>Bundling #{{ $index + 1 }}</strong>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBundlingRow(this)">
                                            Remove
                                        </button>
                                    </div>

                                    <input type="hidden" name="bundlings[{{ $index }}][id]" value="{{ old('bundlings.' . $index . '.id', $bundling['id'] ?? '') }}">

                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label>Label</label>
                                            <input type="text"
                                                name="bundlings[{{ $index }}][label]"
                                                class="form-control @error('bundlings.' . $index . '.label') is-invalid @enderror"
                                                value="{{ old('bundlings.' . $index . '.label', $bundling['label'] ?? '') }}"
                                                placeholder="Contoh: Family Pack">
                                            @error('bundlings.' . $index . '.label')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label>People Count</label>
                                            <input type="number"
                                                min="1"
                                                name="bundlings[{{ $index }}][people_count]"
                                                class="form-control @error('bundlings.' . $index . '.people_count') is-invalid @enderror"
                                                value="{{ old('bundlings.' . $index . '.people_count', $bundling['people_count'] ?? '') }}">
                                            @error('bundlings.' . $index . '.people_count')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label>Bundle Price</label>
                                            <input type="number"
                                                step="0.01"
                                                min="0"
                                                name="bundlings[{{ $index }}][bundle_price]"
                                                class="form-control @error('bundlings.' . $index . '.bundle_price') is-invalid @enderror"
                                                value="{{ old('bundlings.' . $index . '.bundle_price', $bundling['bundle_price'] ?? '') }}">
                                            @error('bundlings.' . $index . '.bundle_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label>Status</label>
                                            <select name="bundlings[{{ $index }}][is_active]" class="form-control">
                                                <option value="1" {{ old('bundlings.' . $index . '.is_active', $bundling['is_active'] ?? true) ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ !old('bundlings.' . $index . '.is_active', $bundling['is_active'] ?? true) ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group mb-0">
                                        <label>Description</label>
                                        <textarea
                                            name="bundlings[{{ $index }}][description]"
                                            rows="2"
                                            class="form-control @error('bundlings.' . $index . '.description') is-invalid @enderror"
                                            placeholder="Catatan opsional untuk bundling ini">{{ old('bundlings.' . $index . '.description', $bundling['description'] ?? '') }}</textarea>
                                        @error('bundlings.' . $index . '.description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3 mb-0">
                                        <label>Bundling Photos</label>
                                        <input type="file"
                                            name="bundlings[{{ $index }}][photos][]"
                                            class="form-control-file @error('bundlings.' . $index . '.photos.*') is-invalid @enderror"
                                            accept="image/*"
                                            multiple>
                                        <small class="form-text text-muted">
                                            Anda bisa upload beberapa foto sekaligus untuk bundling ini.
                                        </small>
                                        @error('bundlings.' . $index . '.photos.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        @if(!empty($bundling['photos']) && count($bundling['photos']) > 0)
                                            <div class="row mt-3">
                                                @foreach($bundling['photos'] as $photo)
                                                    <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                        <div class="border rounded p-2 h-100">
                                                            <img src="{{ $photo->photo_url }}"
                                                                alt="Bundling Photo"
                                                                class="img-fluid rounded mb-2"
                                                                style="height: 120px; width: 100%; object-fit: cover;">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input"
                                                                    id="delete_bundling_photo_{{ $photo->id }}"
                                                                    name="bundlings[{{ $index }}][delete_photo_ids][]"
                                                                    value="{{ $photo->id }}">
                                                                <label class="custom-control-label small" for="delete_bundling_photo_{{ $photo->id }}">
                                                                    Hapus foto
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                    @role('Super Admin')
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
                    @else
                        <input type="hidden" name="vendor_id" value="{{ old('vendor_id', $paketTour->vendor_id ?? auth()->user()->vendor->id ?? '') }}">
                    @endrole

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
function toggleBundlingPrice() {
}

function bundlingRowTemplate(index) {
    return `
        <div class="border rounded p-3 mb-3 bundling-row">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong>Bundling #${index + 1}</strong>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeBundlingRow(this)">
                    Remove
                </button>
            </div>
            <div class="form-row">
                <input type="hidden" name="bundlings[${index}][id]" value="">
                <div class="form-group col-md-4">
                    <label>Label</label>
                    <input type="text" name="bundlings[${index}][label]" class="form-control" placeholder="Contoh: Family Pack">
                </div>
                <div class="form-group col-md-3">
                    <label>People Count</label>
                    <input type="number" min="1" name="bundlings[${index}][people_count]" class="form-control">
                </div>
                <div class="form-group col-md-3">
                    <label>Bundle Price</label>
                    <input type="number" step="0.01" min="0" name="bundlings[${index}][bundle_price]" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Status</label>
                    <select name="bundlings[${index}][is_active]" class="form-control">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="form-group mb-0">
                <label>Description</label>
                <textarea name="bundlings[${index}][description]" rows="2" class="form-control" placeholder="Catatan opsional untuk bundling ini"></textarea>
            </div>
            <div class="form-group mt-3 mb-0">
                <label>Bundling Photos</label>
                <input type="file" name="bundlings[${index}][photos][]" class="form-control-file" accept="image/*" multiple>
                <small class="form-text text-muted">Anda bisa upload beberapa foto sekaligus untuk bundling ini.</small>
            </div>
        </div>
    `;
}

function reindexBundlingRows() {
    const rows = document.querySelectorAll('#bundling-wrapper .bundling-row');

    rows.forEach((row, index) => {
        const title = row.querySelector('strong');
        if (title) {
            title.textContent = `Bundling #${index + 1}`;
        }

        row.querySelectorAll('input, textarea, select').forEach((field) => {
            field.name = field.name.replace(/bundlings\[\d+\]/, `bundlings[${index}]`);
        });
    });
}

function addBundlingRow() {
    const wrapper = document.getElementById('bundling-wrapper');
    const index = wrapper.querySelectorAll('.bundling-row').length;
    wrapper.insertAdjacentHTML('beforeend', bundlingRowTemplate(index));
}

function removeBundlingRow(button) {
    const row = button.closest('.bundling-row');
    if (!row) {
        return;
    }

    row.remove();
    reindexBundlingRows();
}

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
