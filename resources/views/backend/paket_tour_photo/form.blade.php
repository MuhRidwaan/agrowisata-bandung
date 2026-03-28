@extends('backend.main_dashboard')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ isset($photo->id) ? 'Edit Photo' : 'Add Photo' }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('paket-tour-photos.index') }}">Photo Gallery</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($photo->id) ? 'Edit' : 'Add' }} Photo
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
                            {{ isset($photo->id) ? 'Edit Photo Form' : 'Add Photo Form' }}
                        </h3>
                    </div>

                    <form method="POST"
                        action="{{ isset($photo->id) 
                            ? route('paket-tour-photos.update', $photo->id) 
                            : route('paket-tour-photos.store') }}"
                        enctype="multipart/form-data">

                        @csrf
                        @if(isset($photo->id))
                            @method('PUT')
                        @endif

                        <div class="card-body">

                            {{-- Tour Package --}}
                            <div class="form-group">
                                <label for="paket_tour_id">
                                    Tour Package <span class="text-danger">*</span>
                                </label>

                                <select name="paket_tour_id"
                                    id="paket_tour_id"
                                    class="form-control @error('paket_tour_id') is-invalid @enderror"
                                    required>

                                    <option value="">-- Select Tour Package --</option>

                                    @foreach ($paketTours as $paket)
                                        <option value="{{ $paket->id }}"
                                            {{ old('paket_tour_id', $photo->paket_tour_id ?? '') == $paket->id ? 'selected' : '' }}>
                                            {{ $paket->nama_paket }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('paket_tour_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Upload Photos (Multiple) --}}
                            <div class="form-group">
                                <label for="path_foto">
                                    Upload Photos <span class="text-danger">*</span>
                                </label>

                                <input type="file"
                                    name="path_foto[]"
                                    id="path_foto"
                                    accept="image/*"
                                    class="form-control @error('path_foto') is-invalid @enderror"
                                    multiple
                                    @if(!isset($photo->id)) required @endif
                                    onchange="previewImages(event)">
                                <small class="form-text text-muted">
                                    Klik tanda silang pada foto untuk membatalkan upload atau menandai foto lama untuk dihapus.
                                </small>
                                <div id="preview-container" class="photo-grid mt-3"></div>

                                @error('path_foto')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                @if($errors->has('path_foto.*'))
                                    @foreach($errors->get('path_foto.*') as $messages)
                                        @foreach($messages as $msg)
                                            <div class="invalid-feedback d-block">{{ $msg }}</div>
                                        @endforeach
                                    @endforeach
                                @endif

                                {{-- Preview All Existing Photos (edit mode) --}}
                                @if(isset($allPhotos) && $allPhotos->count())
                                    <div class="mt-3">
                                        <label>All Uploaded Photos:</label><br>
                                        <div class="photo-grid">
                                            @foreach($allPhotos as $p)
                                                @php
                                                    $isMarkedForDeletion = in_array($p->id, old('delete_photos', []));
                                                @endphp
                                                <div class="photo-card existing-photo-card {{ $isMarkedForDeletion ? 'is-marked-delete' : '' }}" data-photo-id="{{ $p->id }}">
                                                    <button type="button"
                                                        class="photo-remove-btn photo-delete-btn"
                                                        onclick="toggleExistingPhotoDeletion({{ $p->id }}, this)"
                                                        aria-label="Hapus foto">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <img src="{{ $p->photo_url }}" alt="Photo" class="photo-thumb">
                                                    <input type="checkbox"
                                                        name="delete_photos[]"
                                                        value="{{ $p->id }}"
                                                        class="existing-photo-checkbox d-none"
                                                        id="delete-photo-{{ $p->id }}"
                                                        {{ $isMarkedForDeletion ? 'checked' : '' }}>
                                                    <div class="photo-status text-muted small">{{ $isMarkedForDeletion ? 'Akan dihapus' : 'Tersimpan' }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($photo->id) ? 'Save' : 'Save' }}
                            </button>

                            <a href="{{ route('paket-tour-photos.index') }}"
                                class="btn btn-secondary">
                                Back
                            </a>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>
</section>
@endsection

<style>
.photo-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-start;
}

.photo-card {
    position: relative;
    width: 120px;
    flex: 0 0 120px;
}

.photo-thumb {
    width: 120px;
    height: 120px;
    object-fit: cover;
    object-position: center;
    border-radius: 10px;
    display: block;
    border: 1px solid #dee2e6;
    background: #f8f9fa;
}

.photo-remove-btn {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 24px;
    height: 24px;
    border: none;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.92);
    color: #fff;
    font-size: 16px;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
}

.photo-remove-btn:hover {
    background: #dc3545;
}

.photo-delete-btn {
    font-size: 12px;
}

.photo-status {
    margin-top: 6px;
    text-align: center;
}

.photo-card.is-marked-delete .photo-thumb {
    opacity: 0.35;
    filter: grayscale(1);
}

.photo-card.is-marked-delete .photo-status {
    color: #dc3545 !important;
    font-weight: 600;
}
</style>

@push('scripts')
<script>
const selectedPhotoFiles = new DataTransfer();

function syncPhotoInputFiles() {
    const input = document.getElementById('path_foto');
    if (!input) {
        return;
    }

    input.files = selectedPhotoFiles.files;
}

function createNewPhotoPreview(file, index) {
    const preview = document.getElementById('preview-container');
    if (!preview) {
        return;
    }

    const card = document.createElement('div');
    card.className = 'photo-card';
    card.dataset.fileIndex = index;

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'photo-remove-btn';
    removeBtn.setAttribute('aria-label', 'Batalkan foto');
    removeBtn.innerHTML = '&times;';
    removeBtn.addEventListener('click', function () {
        removeSelectedPhoto(index);
    });

    const img = document.createElement('img');
    img.className = 'photo-thumb';
    img.alt = file.name;

    const status = document.createElement('div');
    status.className = 'photo-status text-muted small';
    status.textContent = file.name;

    const reader = new FileReader();
    reader.onload = function (e) {
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    card.appendChild(removeBtn);
    card.appendChild(img);
    card.appendChild(status);
    preview.appendChild(card);
}

function renderNewPhotoPreviews() {
    const preview = document.getElementById('preview-container');
    if (!preview) {
        return;
    }

    preview.innerHTML = '';

    Array.from(selectedPhotoFiles.files).forEach(function (file, index) {
        createNewPhotoPreview(file, index);
    });
}

function removeSelectedPhoto(index) {
    const rebuiltFiles = new DataTransfer();

    Array.from(selectedPhotoFiles.files).forEach(function (file, fileIndex) {
        if (fileIndex !== index) {
            rebuiltFiles.items.add(file);
        }
    });

    selectedPhotoFiles.items.clear();
    Array.from(rebuiltFiles.files).forEach(function (file) {
        selectedPhotoFiles.items.add(file);
    });

    syncPhotoInputFiles();
    renderNewPhotoPreviews();
}

function previewImages(event) {
    const files = Array.from(event.target.files || []);

    files.forEach(function (file) {
        if (file.type.match('image.*')) {
            selectedPhotoFiles.items.add(file);
        }
    });

    syncPhotoInputFiles();
    renderNewPhotoPreviews();
}

function toggleExistingPhotoDeletion(photoId, button) {
    const checkbox = document.getElementById('delete-photo-' + photoId);
    const card = button.closest('.photo-card');

    if (!checkbox || !card) {
        return;
    }

    const status = card.querySelector('.photo-status');

    if (checkbox.checked) {
        checkbox.checked = false;
        card.classList.remove('is-marked-delete');

        if (status) {
            status.textContent = 'Tersimpan';
        }

        return;
    }

    Swal.fire({
        title: 'Hapus foto ini?',
        text: 'Foto ini akan ditandai untuk dihapus saat Anda menyimpan perubahan.',
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

        checkbox.checked = true;
        card.classList.add('is-marked-delete');

        if (status) {
            status.textContent = 'Akan dihapus';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.existing-photo-checkbox').forEach(function (checkbox) {
        const card = checkbox.closest('.photo-card');
        const status = card ? card.querySelector('.photo-status') : null;

        if (checkbox.checked && card) {
            card.classList.add('is-marked-delete');
        }

        if (status) {
            status.textContent = checkbox.checked ? 'Akan dihapus' : 'Tersimpan';
        }
    });
});
</script>
@endpush
