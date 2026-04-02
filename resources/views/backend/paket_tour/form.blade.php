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
                    @php
                        $hasMinimumPerson = (bool) old('has_minimum_person', $paketTour->has_minimum_person ?? false);
                    @endphp

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

                    <div class="form-group">
                        <label class="d-block">Minimum Person</label>
                        <input type="hidden" name="has_minimum_person" value="0">

                        <div class="minimum-person-option @if($hasMinimumPerson) is-active @endif">
                            <div class="form-check minimum-person-check mb-0">
                                <input
                                    type="checkbox"
                                    class="form-check-input @error('has_minimum_person') is-invalid @enderror"
                                    id="has_minimum_person"
                                    name="has_minimum_person"
                                    value="1"
                                    {{ $hasMinimumPerson ? 'checked' : '' }}
                                    onchange="toggleMinimumPersonField()">
                                <label class="form-check-label" for="has_minimum_person">
                                    Add minimum person
                                </label>
                            </div>
                            <small class="form-text text-muted minimum-person-help mb-0">
                                Centang jika paket ini memiliki jumlah peserta minimum.
                            </small>
                        </div>

                        <div id="minimum-person-field"
                            class="minimum-person-inline mt-2"
                            style="{{ $hasMinimumPerson ? '' : 'display:none;' }}">
                            <label class="minimum-person-input-label">Minimum Participants</label>
                            <div class="input-group">
                                <input type="number"
                                    name="minimum_person"
                                    id="minimum_person"
                                    min="1"
                                    class="form-control @error('minimum_person') is-invalid @enderror"
                                    value="{{ old('minimum_person', $paketTour->minimum_person ?? '') }}"
                                    placeholder="Masukkan jumlah minimum peserta">
                                <div class="input-group-append">
                                    <span class="input-group-text">Pax</span>
                                </div>
                            </div>
                            <small class="form-text text-muted mb-0">
                                Jumlah peserta di tampilan web tidak bisa kurang dari angka ini.
                            </small>
                            @error('minimum_person')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        @error('has_minimum_person')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                            class="form-control-file bundling-photo-input @error('bundlings.' . $index . '.photos.*') is-invalid @enderror"
                                            accept="image/*"
                                            multiple>
                                        <small class="form-text text-muted">
                                            Anda bisa upload beberapa foto sekaligus untuk bundling ini.
                                        </small>
                                        @error('bundlings.' . $index . '.photos.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="mt-3 bundling-photo-preview-list"></div>

                                        @if(!empty($bundling['photos']) && count($bundling['photos']) > 0)
                                            <div class="row mt-3">
                                                @foreach($bundling['photos'] as $photo)
                                                    <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                        <div class="border rounded p-2 h-100 bundling-photo-card">
                                                            <img src="{{ $photo->photo_url }}"
                                                                alt="Bundling Photo"
                                                                class="img-fluid rounded"
                                                                style="height: 120px; width: 100%; object-fit: cover;">
                                                            <input type="checkbox"
                                                                    class="d-none bundling-photo-delete-input"
                                                                    id="delete_bundling_photo_{{ $photo->id }}"
                                                                    name="bundlings[{{ $index }}][delete_photo_ids][]"
                                                                    value="{{ $photo->id }}">
                                                            <div class="bundling-photo-card-footer">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger bundling-photo-delete-btn"
                                                                    data-target="#delete_bundling_photo_{{ $photo->id }}"
                                                                    aria-label="Hapus foto bundling"
                                                                    onclick="toggleBundlingPhotoDelete(this)">
                                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                                </button>
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
                                            class="btn btn-danger"
                                            onclick="removeActivity(this)">-</button>
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

                    {{-- FACILITIES --}}
                    <div class="form-group">
                        <label>Facilities <small class="text-muted">(Optional)</small></label>

                        <div id="facility-wrapper">

                            @if(old('facilities'))
                                @foreach(old('facilities') as $item)
                                    <div class="input-group mb-2">
                                        <input type="text"
                                            name="facilities[]"
                                            class="form-control"
                                            value="{{ $item }}"
                                            placeholder="Contoh: Toilet, Mushola, Area Parkir">
                                        <div class="input-group-append">
                                            <button type="button"
                                                class="btn btn-danger"
                                                onclick="removeFacility(this)">-</button>
                                        </div>
                                    </div>
                                @endforeach

                            @elseif(isset($paketTour->facilities) && is_array($paketTour->facilities))
                                @foreach($paketTour->facilities as $item)
                                    <div class="input-group mb-2">
                                        <input type="text"
                                            name="facilities[]"
                                            class="form-control"
                                            value="{{ $item }}"
                                            placeholder="Contoh: Toilet, Mushola, Area Parkir">
                                        <div class="input-group-append">
                                            <button type="button"
                                                class="btn btn-danger"
                                                onclick="removeFacility(this)">-</button>
                                        </div>
                                    </div>
                                @endforeach

                            @else
                                <div class="input-group mb-2">
                                    <input type="text"
                                        name="facilities[]"
                                        class="form-control"
                                        placeholder="Contoh: Toilet, Mushola, Area Parkir">
                                    <div class="input-group-append">
                                        <button type="button"
                                            class="btn btn-danger"
                                            onclick="removeFacility(this)">-</button>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <button type="button"
                            class="btn btn-sm btn-secondary mt-2"
                            onclick="addFacility()">
                            + Add Facility
                        </button>

                        @error('facilities')
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

@push('styles')
<style>
.bundling-photo-card {
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.minimum-person-option {
    border: 1px solid #d9e2ec;
    border-radius: 10px;
    padding: 12px 14px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.minimum-person-option.is-active {
    border-color: #9ec5fe;
    background: #f8fbff;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.08);
}

.minimum-person-check .form-check-label {
    font-weight: 600;
    color: #1f2937;
}

.minimum-person-help {
    margin-left: 1.5rem;
    line-height: 1.45;
}

.minimum-person-inline {
    margin-left: 1.5rem;
}

.minimum-person-input-label {
    display: inline-block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
}

.bundling-photo-delete-btn {
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.bundling-photo-card-footer {
    display: flex;
    justify-content: flex-end;
    padding-top: 0.75rem;
}

.bundling-photo-card.is-marked {
    border-color: #dc3545 !important;
    background: rgba(220, 53, 69, 0.06);
}

.bundling-photo-card.is-marked img {
    opacity: 0.45;
}

.bundling-photo-preview-list {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 8px;
}

.bundling-photo-preview-item {
    width: 56px;
    flex: 0 0 56px;
}

.bundling-photo-preview-card {
    position: relative;
    width: 56px;
    height: 56px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.bundling-photo-preview-card img {
    width: 100%;
    height: 56px;
    object-fit: cover;
    display: block;
}

.bundling-photo-preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 18px;
    height: 18px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.94);
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.14);
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
}
</style>
@endpush

@section('scripts')
<script>
function toggleBundlingPrice() {
}

function toggleMinimumPersonField() {
    const toggle = document.getElementById('has_minimum_person');
    const wrapper = document.getElementById('minimum-person-field');
    const input = document.getElementById('minimum_person');
    const option = document.querySelector('.minimum-person-option');

    if (!toggle || !wrapper || !input) {
        return;
    }

    if (toggle.checked) {
        wrapper.style.display = '';
        input.setAttribute('min', '1');
        option?.classList.add('is-active');
        return;
    }

    wrapper.style.display = 'none';
    input.value = '';
    option?.classList.remove('is-active');
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
                <input type="file" name="bundlings[${index}][photos][]" class="form-control-file bundling-photo-input" accept="image/*" multiple>
                <small class="form-text text-muted">Anda bisa upload beberapa foto sekaligus untuk bundling ini.</small>
                <div class="mt-3 bundling-photo-preview-list"></div>
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

function addFacility() {
    const wrapper = document.getElementById('facility-wrapper');

    const div = document.createElement('div');
    div.classList.add('input-group', 'mb-2');

    div.innerHTML = `
        <input type="text" name="facilities[]" class="form-control" placeholder="Contoh: Toilet, Mushola, Area Parkir">
        <div class="input-group-append">
            <button type="button" class="btn btn-danger" onclick="removeFacility(this)">-</button>
        </div>
    `;

    wrapper.appendChild(div);
}

function removeFacility(button) {
    button.closest('.input-group').remove();
}

function renderBundlingPhotoPreview(input) {
    const previewList = input.closest('.form-group')?.querySelector('.bundling-photo-preview-list');
    if (!previewList) {
        return;
    }

    previewList.style.display = 'flex';
    previewList.style.flexDirection = 'row';
    previewList.style.flexWrap = 'wrap';
    previewList.style.alignItems = 'flex-start';
    previewList.style.gap = '8px';

    previewList.innerHTML = '';

    Array.from(input.files || []).forEach((file, index) => {
        if (!file.type.startsWith('image/')) {
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        const item = document.createElement('div');
        item.className = 'bundling-photo-preview-item';
        item.style.width = '56px';
        item.style.height = '56px';
        item.style.flex = '0 0 56px';
        item.style.display = 'inline-block';
        item.style.verticalAlign = 'top';
        item.innerHTML = `
            <div class="bundling-photo-preview-card" style="position:relative;width:56px;height:56px;border:1px solid #dee2e6;border-radius:8px;overflow:hidden;background:#fff;">
                <img src="${objectUrl}" alt="Preview foto bundling ${index + 1}" style="width:56px;height:56px;object-fit:cover;display:block;">
                <button type="button"
                    class="bundling-photo-preview-remove"
                    style="position:absolute;top:4px;right:4px;width:18px;height:18px;border:none;border-radius:999px;background:rgba(255,255,255,.94);color:#6c757d;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 6px 16px rgba(15,23,42,.14);font-size:12px;font-weight:700;line-height:1;padding:0;"
                    aria-label="Batalkan foto"
                    onclick="removeBundlingSelectedPhoto(this, ${index})">
                    &times;
                </button>
            </div>
        `;

        const img = item.querySelector('img');
        if (img) {
            img.onload = () => URL.revokeObjectURL(objectUrl);
        }

        previewList.appendChild(item);
    });
}

function removeBundlingSelectedPhoto(button, index) {
    const previewList = button.closest('.bundling-photo-preview-list');
    const formGroup = previewList?.closest('.form-group');
    const input = formGroup?.querySelector('.bundling-photo-input');

    if (!input || typeof DataTransfer === 'undefined') {
        return;
    }

    const dt = new DataTransfer();
    Array.from(input.files || []).forEach((file, fileIndex) => {
        if (fileIndex !== index) {
            dt.items.add(file);
        }
    });

    input.files = dt.files;
    renderBundlingPhotoPreview(input);
}

function toggleBundlingPhotoDelete(button) {
    const targetSelector = button.dataset.target;
    const input = document.querySelector(targetSelector);
    const card = button.closest('.bundling-photo-card');

    if (!input || !card) {
        return;
    }

    if (input.checked) {
        input.checked = false;
        card.classList.remove('is-marked');
        button.classList.remove('btn-secondary');
        button.classList.add('btn-outline-danger');
        return;
    }

    Swal.fire({
        title: 'Hapus foto ini?',
        text: 'Foto akan ditandai untuk dihapus saat data bundling disimpan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (!result.isConfirmed) {
            return;
        }

        input.checked = true;
        card.classList.add('is-marked');
        button.classList.remove('btn-outline-danger');
        button.classList.add('btn-secondary');
    });
}

document.addEventListener('change', function (event) {
    if (event.target.matches('.bundling-photo-input')) {
        renderBundlingPhotoPreview(event.target);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    toggleMinimumPersonField();
});
</script>
@endsection
