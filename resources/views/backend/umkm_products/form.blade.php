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
                                <h4 class="alert-heading">❌ Ada kesalahan!</h4>
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
                                <label for="paket_tour_id">
                                    Paket Tour <span class="text-danger">*</span>
                                </label>

                                <select name="paket_tour_id" id="paket_tour_id"
                                        class="form-control @error('paket_tour_id') is-invalid @enderror"
                                        onchange="updateVendorFromPaketTour()">
                                    <option value="">Select Paket Tour</option>
                                    @foreach ($paketTours as $paket)
                                        @php
                                            // Get the first paket tour if editing
                                            $selectedPaketTour = old('paket_tour_id', 
                                                (isset($umkmProduct->id) && $umkmProduct->paketTours->count() > 0) 
                                                    ? $umkmProduct->paketTours->first()->id 
                                                    : null
                                            );
                                        @endphp
                                        <option value="{{ $paket->id }}" 
                                                data-vendor-id="{{ $paket->vendor_id }}"
                                                data-vendor-name="{{ $paket->vendor->name ?? '' }}"
                                                {{ $selectedPaketTour == $paket->id ? 'selected' : '' }}>
                                            {{ $paket->nama_paket }} ({{ $paket->vendor->name ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('paket_tour_id')
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

                                <div class="input-group" style="height: 60px;">
                                    <input type="file"
                                           name="path_foto[]"
                                           id="path_foto"
                                           accept="image/*"
                                           class="form-control @error('path_foto') is-invalid @enderror"
                                           multiple
                                           onchange="previewImages(event)"
                                           style="padding: 15px; font-size: 14px;">
                                </div>
                                
                                <!-- <small class="form-text text-muted d-block mt-2">
                                    📁 Format: JPEG, PNG, JPG, GIF | Max: 5MB per file | Bisa upload multiple
                                </small> -->

                                    <!-- <div class="row" id="preview-container" style="margin-left: -10px; margin-right: -10px; margin-top: 20px; background: #f8f9fa; border-radius: 8px; padding: 20px; margin-left: -10px; margin-right: -10px;"></div> -->

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
                                                            <img src="{{ asset('storage/' . $photo->path_foto) }}" 
                                                                 alt="Photo" 
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
                                                                <small style="color: #666;">{{ basename($photo->path_foto) }}</small>
                                                            </div>
                                                            
                                                            <form method="POST" 
                                                                  action="{{ route('umkm-product-photos.destroy', $photo->id) }}" 
                                                                  style="margin: 0;" 
                                                                  id="deleteForm_{{ $photo->id }}"
                                                                  onsubmit="return confirm('Hapus foto ini?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <div style="display: flex; align-items: center; gap: 6px;">
                                                                    <input type="checkbox" 
                                                                           id="checkbox_{{ $photo->id }}"
                                                                           name="delete_{{ $photo->id }}" 
                                                                           style="cursor: pointer; width: 18px; height: 18px; accent-color: #dc3545;">
                                                                    <button type="button" 
                                                                            class="btn btn-danger btn-sm"
                                                                            style="padding: 4px 8px; font-size: 12px; margin: 0;"
                                                                            onclick="handleDeleteClick('{{ $photo->id }}')">
                                                                        <i class="fas fa-trash-alt"></i> Delete
                                                                    </button>
                                                                </div>
                                                            </form>
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
                                {{ isset($umkmProduct->id) ? 'Update' : 'Create' }} Product
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

<script>
function previewImages(event) {
    const files = event.target.files;
    const preview = document.getElementById('preview-container');
    preview.innerHTML = '';
    
    if (files) {
        let validFileCount = 0;
        const container = document.createElement('div');
        container.className = 'row';
        container.style.marginTop = '20px';
        container.style.marginLeft = '-12px';
        container.style.marginRight = '-12px';
        
        Array.from(files).forEach(file => {
            if (file.type.match('image.*')) {
                validFileCount++;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const colDiv = document.createElement('div');
                    colDiv.className = 'col-lg-3 col-md-4 col-sm-6 mb-4';
                    colDiv.style.padding = '10px';
                    
                    const cardDiv = document.createElement('div');
                    cardDiv.style.cssText = 'background: white; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; transition: all 0.3s ease; position: relative; height: 100%;';
                    
                    const imageContainer = document.createElement('div');
                    imageContainer.style.cssText = 'position: relative; background: #f0f0f0; height: 220px;';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
                    
                    const badge = document.createElement('span');
                    badge.innerHTML = '<i class="far fa-image"></i> Photo';
                    badge.style.cssText = 'position: absolute; top: 8px; right: 8px; background: rgba(255,255,255,0.9); color: #333; font-size: 12px; padding: 4px 8px; border-radius: 4px;';
                    
                    imageContainer.appendChild(img);
                    imageContainer.appendChild(badge);
                    
                    const infoDiv = document.createElement('div');
                    infoDiv.style.cssText = 'padding: 12px;';
                    
                    const label = document.createElement('small');
                    label.textContent = file.name;
                    label.style.cssText = 'display: block; font-size: 13px; color: #666; word-break: break-all; margin-bottom: 8px;';
                    
                    infoDiv.appendChild(label);
                    cardDiv.appendChild(imageContainer);
                    cardDiv.appendChild(infoDiv);
                    colDiv.appendChild(cardDiv);
                    container.appendChild(colDiv);
                };
                reader.readAsDataURL(file);
            }
        });
        
        if (validFileCount > 0) {
            preview.appendChild(container);
            preview.insertAdjacentHTML('beforeend', `<div class="alert alert-info mt-3"><i class="fas fa-info-circle"></i> <strong>${validFileCount} foto</strong> siap di-upload</div>`);
        } else {
            preview.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Tidak ada file gambar yang valid</div>';
        }
    }
}

function handleDeleteClick(photoId) {
    const checkbox = document.getElementById(`checkbox_${photoId}`);
    const form = document.getElementById(`deleteForm_${photoId}`);
    
    if (!checkbox.checked) {
        alert('Pilih checkbox dulu untuk konfirmasi penghapusan');
        checkbox.focus();
        return false;
    }
    
    // Checkbox sudah dicek, submit form
    form.submit();
}

function updateVendorFromPaketTour() {
    const paketTourSelect = document.getElementById('paket_tour_id');
    const selectedOption = paketTourSelect.options[paketTourSelect.selectedIndex];
    
    if (selectedOption.value) {
        const vendorId = selectedOption.getAttribute('data-vendor-id');
        const vendorName = selectedOption.getAttribute('data-vendor-name');
        
        document.getElementById('vendor_id').value = vendorId;
        document.getElementById('vendor_display').value = vendorName;
    } else {
        document.getElementById('vendor_id').value = '';
        document.getElementById('vendor_display').value = '';
    }
}

// Trigger on page load if editing
document.addEventListener('DOMContentLoaded', function() {
    const paketTourSelect = document.getElementById('paket_tour_id');
    const vendorIdField = document.getElementById('vendor_id');
    
    // Initialize vendor field on page load
    if (paketTourSelect.value) {
        updateVendorFromPaketTour();
    } else if (vendorIdField.value) {
        // If paket_tour not selected but vendor_id exists (editing), 
        // try to find and select the correct paket tour
        let selectedIndex = -1;
        for (let i = 0; i < paketTourSelect.options.length; i++) {
            if (paketTourSelect.options[i].getAttribute('data-vendor-id') == vendorIdField.value) {
                selectedIndex = i;
                break;
            }
        }
        if (selectedIndex >= 0) {
            paketTourSelect.selectedIndex = selectedIndex;
            updateVendorFromPaketTour();
        }
    }
});

// Add form submit validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const paketTourSelect = document.getElementById('paket_tour_id');
    const vendorIdField = document.getElementById('vendor_id');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered');
            console.log('initialVendorId:', vendorIdField.value);
            console.log('paketTourSelect:', paketTourSelect.value);
            
            // Get initial vendor_id value (for checking if we're editing)
            const initialVendorId = vendorIdField.value;
            
            // If editing (vendor_id already exists), allow submit without requiring paketTourSelect
            if (initialVendorId) {
                console.log('Editing mode - allowing submit');
                return true;
            }
            
            // For new product: require paketTourSelect
            if (!paketTourSelect.value) {
                e.preventDefault();
                alert('Mohon pilih Paket Tour terlebih dahulu');
                paketTourSelect.focus();
                return false;
            }
            
            // Try to populate vendor_id if not yet set
            if (!vendorIdField.value && paketTourSelect.value) {
                updateVendorFromPaketTour();
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
    
    /* Checkbox styling */
    input[type="checkbox"] {
        accent-color: #dc3545;
    }
</style>

<script>
    // Handle photo preview modal click
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('photo-preview-thumb')) {
            var imageUrl = e.target.getAttribute('data-image');
            document.getElementById('previewImage').src = imageUrl;
        }
    });
</script>

@endsection