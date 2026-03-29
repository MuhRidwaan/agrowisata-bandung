@extends('backend.main_dashboard')

@section('content')

{{-- Content Header --}}
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    {{ isset($umkmProduct->id) ? 'Edit UMKM Product' : 'Add UMKM Product' }}
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('umkm-products.index') }}">
                            UMKM Products
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($umkmProduct->id) ? 'Edit' : 'Add' }} UMKM Product
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
                            {{ isset($umkmProduct->id) ? 'Edit UMKM Product' : 'Add UMKM Product' }}
                        </h3>
                    </div>

                    <form method="POST"
                          action="{{ isset($umkmProduct->id)
                              ? route('umkm-products.update', $umkmProduct)
                              : route('umkm-products.store') }}"
                          enctype="multipart/form-data"
                          id="umkm-form">

                        @csrf

                        {{-- Display All Validation Errors --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h4 class="alert-heading">Ada kesalahan!</h4>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (isset($umkmProduct->id))
                            @method('PUT')
                        @endif

                        <div class="card-body">

                            {{-- Paket Tour Selection --}}
                            <div class="form-group">
                                <label for="paket_tour_ids">
                                    Paket Tour <span class="text-danger">*</span>
                                </label>

                                @php
                                    $selectedPaketTourIds = collect(old(
                                        'paket_tour_ids',
                                        isset($umkmProduct->id) ? $umkmProduct->paketTours->pluck('id')->all() : []
                                    ))->map(fn ($id) => (int) $id)->all();
                                @endphp

                                <select name="paket_tour_ids[]" id="paket_tour_ids"
                                        class="form-control @error('paket_tour_ids') is-invalid @enderror @error('paket_tour_ids.*') is-invalid @enderror"
                                        multiple
                                        size="6"
                                        onchange="updateVendorFromPaketTour()">
                                    @foreach ($paketTours as $paket)
                                        <option value="{{ $paket->id }}" 
                                                data-vendor-id="{{ $paket->vendor_id }}"
                                                data-vendor-name="{{ $paket->vendor->name ?? '' }}"
                                                {{ in_array((int) $paket->id, $selectedPaketTourIds, true) ? 'selected' : '' }}>
                                            {{ $paket->nama_paket }} ({{ $paket->vendor->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted mt-2">
                                    Bisa pilih lebih dari satu paket tour, tetapi semuanya harus berasal dari vendor yang sama.
                                </small>
                                @error('paket_tour_ids')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @error('paket_tour_ids.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Vendor Name (Auto-populated) --}}
                            <div class="form-group">
                                <label for="vendor_id">
                                    Vendor Name <span class="text-danger">*</span>
                                </label>

                                <input type="text" id="vendor_display"
                                       class="form-control"
                                       readonly
                                       placeholder="Vendor will auto-populate when you select a paket tour">

                                <input type="hidden" name="vendor_id" id="vendor_id"
                                       value="{{ old('vendor_id', $umkmProduct->vendor_id ?? '') }}">
                                
                                @error('vendor_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Product Name --}}
                            <div class="form-group">
                                <label for="name">
                                    Product Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $umkmProduct->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>  

                            {{-- Description --}}
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="4">{{ old('description', $umkmProduct->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Price --}}
                            <div class="form-group">
                                <label for="price">
                                    Price <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="price" id="price" step="0.01"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price', $umkmProduct->price ?? '') }}" required>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Product Photos --}}
                            <div class="form-group">
                                <label for="path_foto">
                                    <strong>Add Photo</strong>
                                </label>

                                <div class="custom-file-upload">
                                    <input type="file"
                                           name="path_foto[]"
                                           id="path_foto"
                                           accept="image/*"
                                           class="form-control @error('path_foto') is-invalid @enderror"
                                           multiple
                                           onchange="previewImages(event)"
                                           hidden>
                                    <label for="path_foto" class="custom-file-upload__label mb-0">
                                        <span class="custom-file-upload__button">
                                            Choose Files
                                        </span>
                                        <span class="custom-file-upload__text" id="path_foto_text">
                                            No file chosen
                                        </span>
                                    </label>
                                </div>
                                
                                <!-- <small class="form-text text-muted d-block mt-2">
                                    📁 Format: JPEG, PNG, JPG, GIF | Max: 5MB per file | Bisa upload multiple
                                </small> -->

                                <small class="form-text text-muted d-block mt-2">
                                    Format: JPEG, PNG, JPG, GIF. Bisa pilih lebih dari satu file.
                                </small>

                                <div id="preview-container"
                                     style="margin-top: 20px; background: #f8f9fa; border-radius: 8px; padding: 20px; display: none;"></div>

                                @error('path_foto')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @if($errors->has('path_foto.*'))
                                    @foreach($errors->get('path_foto.*') as $messages)
                                        @foreach($messages as $msg)
                                            <div class="invalid-feedback d-block">{{ $msg }}</div>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>

                            @if(isset($umkmProduct->id) && $umkmProduct->photos->count() > 0)
                                <div class="form-group">
                                    <label>
                                        <strong> All Uploaded Photos:</strong>
                                        <!-- <span class="badge badge-info">{{ $umkmProduct->photos->count() }} foto</span> -->
                                    </label>
                                    
                                    <div style="background: #f8f9fa; border-radius: 8px; padding: 20px;">
                                        <div class="row" style="margin-left: -10px; margin-right: -10px;">
                                            @foreach($umkmProduct->photos as $photo)
                                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4" style="padding: 10px;">
                                                    <div style="background: white; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; position: relative; height: 100%;">
                                                        <div style="position: relative; background: #f0f0f0; height: 220px;">
                                                            <img src="{{ $photo->photo_url }}" 
                                                                 alt="Photo" 
                                                                 data-image="{{ $photo->photo_url }}"
                                                                 style="width: 100%; height: 100%; object-fit: cover; cursor: pointer; display: block;"
                                                                 class="photo-preview-thumb">
                                                            
                                                            <div style="position: absolute; top: 8px; right: 8px;">
                                                                <span class="badge badge-secondary" style="background: rgba(255,255,255,0.9); color: #333; font-size: 12px; padding: 4px 8px;">
                                                                    <i class="far fa-image"></i> Photo
                                                                </span>
                                                            </div>
                                                        </div>
                                                        
                                                        <div style="padding: 12px;">
                                                            <div style="margin-bottom: 8px;">
                                                                <small style="color: #666; display: block; word-break: break-word;">{{ basename($photo->path_foto) }}</small>
                                                            </div>
                                                            
                                                            <div class="photo-delete-row">
                                                                <button type="button" 
                                                                        class="btn btn-danger btn-sm"
                                                                        style="padding: 4px 10px; font-size: 12px; margin: 0;"
                                                                        onclick="handleDeleteClick('{{ route('umkm-product-photos.destroy', $photo->id) }}')">
                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($umkmProduct->id) ? 'Save' : 'Create' }} 
                            </button>

                            <a href="{{ route('umkm-products.index') }}"
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

<form method="POST" id="photoDeleteForm" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
let selectedUmkmFiles = [];

function previewImages(event) {
    const files = Array.from(event.target.files || []);
    const preview = document.getElementById('preview-container');
    const fileText = document.getElementById('path_foto_text');
    const input = document.getElementById('path_foto');

    if (!preview) {
        return;
    }

    files.forEach(function(file) {
        if (file.type.match('image.*')) {
            selectedUmkmFiles.push(file);
        }
    });

    syncUmkmFileInput(input);
    renderUmkmPreview();
}

function syncUmkmFileInput(input) {
    if (!input) {
        return;
    }

    const dataTransfer = new DataTransfer();
    selectedUmkmFiles.forEach(function(file) {
        dataTransfer.items.add(file);
    });
    input.files = dataTransfer.files;
}

function removeSelectedUmkmFile(index) {
    const input = document.getElementById('path_foto');
    selectedUmkmFiles.splice(index, 1);
    syncUmkmFileInput(input);
    renderUmkmPreview();
}

function renderUmkmPreview() {
    const preview = document.getElementById('preview-container');
    const fileText = document.getElementById('path_foto_text');

    if (!preview) {
        return;
    }

    preview.innerHTML = '';
    preview.style.display = 'none';

    if (fileText) {
        fileText.textContent = selectedUmkmFiles.length === 0
            ? 'No file chosen'
            : (selectedUmkmFiles.length === 1 ? selectedUmkmFiles[0].name : `${selectedUmkmFiles.length} files selected`);
    }

    if (selectedUmkmFiles.length === 0) {
        return;
    }

    const container = document.createElement('div');
    container.className = 'row';
    container.style.marginLeft = '-12px';
    container.style.marginRight = '-12px';

    selectedUmkmFiles.forEach(function(file, index) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
            colDiv.style.padding = '10px';

            const cardDiv = document.createElement('div');
            cardDiv.className = 'umkm-upload-preview-card';

            const imageContainer = document.createElement('div');
            imageContainer.className = 'umkm-upload-preview-image';

            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'umkm-upload-preview-photo';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.className = 'umkm-preview-remove-btn';
            removeBtn.setAttribute('aria-label', 'Batal pilih foto');
            removeBtn.onclick = function() {
                removeSelectedUmkmFile(index);
            };

            imageContainer.appendChild(img);
            imageContainer.appendChild(removeBtn);

            const infoDiv = document.createElement('div');
            infoDiv.className = 'umkm-upload-preview-info';

            const label = document.createElement('small');
            label.textContent = file.name;
            label.className = 'umkm-upload-preview-name';

            infoDiv.appendChild(label);
            cardDiv.appendChild(imageContainer);
            cardDiv.appendChild(infoDiv);
            colDiv.appendChild(cardDiv);
            container.appendChild(colDiv);
        };
        reader.readAsDataURL(file);
    });

    preview.style.display = 'block';
    preview.appendChild(container);
}

function handleDeleteClick(deleteUrl) {
    const form = document.getElementById('photoDeleteForm');

    Swal.fire({
        title: 'Hapus foto ini?',
        text: 'Foto yang dihapus tidak bisa dikembalikan.',
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

        form.action = deleteUrl;
        form.submit();
    });
}

function updateVendorFromPaketTour() {
    const paketTourSelect = document.getElementById('paket_tour_ids');
    const selectedOptions = Array.from(paketTourSelect.selectedOptions || []);

    if (selectedOptions.length > 0) {
        const vendorIds = [...new Set(selectedOptions.map(option => option.getAttribute('data-vendor-id')).filter(Boolean))];
        const vendorNames = [...new Set(selectedOptions.map(option => option.getAttribute('data-vendor-name')).filter(Boolean))];

        if (vendorIds.length === 1) {
            document.getElementById('vendor_id').value = vendorIds[0];
            document.getElementById('vendor_display').value = vendorNames[0] ?? '';
        } else {
            document.getElementById('vendor_id').value = '';
            document.getElementById('vendor_display').value = 'Pilih paket tour dari vendor yang sama';
        }
    } else {
        document.getElementById('vendor_id').value = '';
        document.getElementById('vendor_display').value = '';
    }
}

// Trigger on page load if editing
document.addEventListener('DOMContentLoaded', function() {
    updateVendorFromPaketTour();
});

// Add form submit validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const paketTourSelect = document.getElementById('paket_tour_ids');
    const vendorIdField = document.getElementById('vendor_id');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedOptions = Array.from(paketTourSelect.selectedOptions || []);

            if (selectedOptions.length === 0) {
                e.preventDefault();
                alert('Mohon pilih minimal satu Paket Tour terlebih dahulu');
                paketTourSelect.focus();
                return false;
            }

            updateVendorFromPaketTour();

            if (!vendorIdField.value) {
                e.preventDefault();
                alert('Pilih paket tour yang berasal dari vendor yang sama');
                paketTourSelect.focus();
                return false;
            }

            return true;
        });
    }
    
    // Photo preview thumbnail hover effect
    var photoThumbs = document.querySelectorAll('.photo-preview-thumb');
    photoThumbs.forEach(function(thumb) {
        thumb.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
        });
        thumb.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>

{{-- Photo Preview Modal (for form) --}}
<div class="modal fade" id="photoPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Photo Preview</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 600px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<style>
    .custom-file-upload__label {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        min-height: 54px;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        border-radius: 6px;
        background: #ffffff;
        cursor: pointer;
        transition: border-color 0.2s ease;
    }

    .custom-file-upload__label:hover {
        border-color: #adb5bd;
    }

    .custom-file-upload__button {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 2px;
        background: #f8f9fa;
        color: #212529;
        font-weight: 400;
        border: 1px solid #6c757d;
        white-space: nowrap;
        line-height: 1.2;
    }

    .custom-file-upload__text {
        color: #6c757d;
        font-size: 15px;
        word-break: break-word;
    }

    .photo-preview-thumb {
        transition: transform 0.3s ease, filter 0.3s ease;
    }
    
    .photo-preview-thumb:hover {
        transform: scale(1.08);
        filter: brightness(0.95);
        cursor: pointer;
    }
    
    /* Gallery animation */
    @keyframes galleryFadeIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    
    #preview-container > div {
        animation: galleryFadeIn 0.4s ease-in;
    }
    
    /* Card hover effect */
    #preview-container > div > div {
        transition: all 0.3s ease;
    }
    
    #preview-container > div:hover > div {
        border-color: #0066cc !important;
        box-shadow: 0 8px 16px rgba(0, 102, 204, 0.2) !important;
        transform: translateY(-4px);
    }
    
    .photo-delete-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .umkm-upload-preview-card {
        background: #fff;
        border: 1px solid #d9dee5;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        height: 100%;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
    }

    .umkm-upload-preview-image {
        position: relative;
        background: #f0f0f0;
        height: 220px;
    }

    .umkm-upload-preview-photo {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .umkm-upload-preview-info {
        padding: 12px 14px;
    }

    .umkm-upload-preview-name {
        display: block;
        font-size: 13px;
        color: #667085;
        word-break: break-word;
    }

    .umkm-preview-remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: rgba(17, 24, 39, 0.92);
        color: #fff;
        font-size: 22px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        transition: transform 0.2s ease, background 0.2s ease;
        z-index: 2;
    }

    .umkm-preview-remove-btn:hover {
        background: rgba(220, 53, 69, 0.96);
        transform: scale(1.06);
    }
</style>

<script>
    // Handle photo preview modal click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('photo-preview-thumb')) {
            var imageUrl = e.target.getAttribute('data-image');
            document.getElementById('previewImage').src = imageUrl;
            $('#photoPreviewModal').modal('show');
        }
    });
</script>

@endsection
