@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>UMKM Products</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">UMKM Products</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        {{-- Success/Error Messages --}}
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ $message }}
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ $message }}
                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h3 class="card-title mb-0">UMKM Products</h3>

                <div class="ml-auto d-flex align-items-center gap-2">

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('umkm-products.index') }}" class="d-flex align-items-center mr-2">
                        @if(request('created_from'))<input type="hidden" name="created_from" value="{{ request('created_from') }}">@endif
                        @if(request('created_to'))<input type="hidden" name="created_to" value="{{ request('created_to') }}">@endif
                        <div class="input-group input-group-sm" style="width:220px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama produk...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>

                    {{-- Add Button --}}
                    <a href="{{ route('umkm-products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                </div>
            </div>

            {{-- Filter Form --}}
            <div class="card-body">
                <form method="GET" action="{{ route('umkm-products.index') }}" class="mb-3 d-flex align-items-center flex-wrap">
                    @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                    
                    @role('Super Admin')
                    <label class="mr-2 mb-0">Vendor:</label>
                    <select name="vendor_id" class="form-control mr-3 mb-1" style="width:auto;">
                        <option value="">Semua Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    @endrole

                    <label class="mr-2 mb-0">Tanggal dibuat:</label>
                    <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control mr-2 mb-1" style="width:auto;">
                    <span class="mx-1">s/d</span>
                    <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control mr-2 mb-1" style="width:auto;">
                    <button type="submit" class="btn btn-secondary btn-sm mb-1">Filter</button>
                    
                    @if(request('search') || request('created_from') || request('created_to') || request('vendor_id'))
                        <a href="{{ route('umkm-products.index') }}" class="btn btn-link btn-sm ml-2 mb-1">Reset</a>
                    @endif
                </form>

                {{-- Products Table --}}
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Photo</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Vendor</th>
                            <th class="text-right price-column">Price</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($umkmProducts as $product)
                            <tr>
                                <td>{{ $loop->iteration + ($umkmProducts->currentPage() - 1) * $umkmProducts->perPage() }}</td>

                                {{-- Photo --}}
                                <td class="text-center">
                                    @php
                                        $photoUrls = $product->photos->map(fn ($photo) => $photo->photo_url)->filter()->values();
                                    @endphp
                                    @if($photoUrls->count() > 0)
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;">
                                            @foreach($product->photos->take(3) as $photo)
                                                <a href="#" 
                                                   class="photo-preview-trigger"
                                                   data-product-name="{{ $product->name }}"
                                                   data-photos='@json($photoUrls)'
                                                   style="display: inline-block;">
                                                    <img src="{{ $photo->photo_url }}" 
                                                         alt="Photo" 
                                                         style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #ddd; display: block;"
                                                         class="img-hover">
                                                </a>
                                            @endforeach
                                            @if($photoUrls->count() > 3)
                                                <div style="width: 48px; height: 48px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid #ddd;">
                                                    <small><strong>+{{ $photoUrls->count() - 3 }}</strong></small>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge badge-secondary">Belum ada foto</span>
                                    @endif
                                </td>

                                {{-- Product Name --}}
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                </td>

                                {{-- Description --}}
                                <td>
                                    {{ Str::limit($product->description, 50) ?? '-' }}
                                </td>

                                {{-- Vendor --}}
                                <td>
                                    {{ $product->vendor->name ?? '-' }}
                                </td>

                                {{-- Price --}}
                                <td class="text-right price-column">
                                    @if($product->price)
                                        <strong class="price-value">Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <a href="{{ route('umkm-products.edit', $product) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('umkm-products.destroy', $product) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-box"></i> No products found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($umkmProducts->hasPages())
                <div class="card-footer">
                    {{ $umkmProducts->links() }}
                </div>
            @endif

        </div>

    </div>
</section>

<div id="umkmPhotoLightbox" class="backend-lightbox" onclick="closeUmkmPhotoPreview()">
    <button class="backend-lightbox-close" onclick="event.stopPropagation(); closeUmkmPhotoPreview()">✕</button>
    <button class="backend-lightbox-nav prev d-none" id="umkmPhotoPrev" onclick="event.stopPropagation(); prevUmkmPhoto()">❮</button>
    <img id="umkmPhotoPreviewImage" onclick="event.stopPropagation()">
    <button class="backend-lightbox-nav next d-none" id="umkmPhotoNext" onclick="event.stopPropagation(); nextUmkmPhoto()">❯</button>
</div>

<style>
    .img-hover {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .img-hover:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    .photo-preview-trigger {
        cursor: pointer;
        transition: transform 0.2s, filter 0.2s;
        display: inline-block;
    }
    
    .photo-preview-trigger:hover {
        transform: scale(1.1);
        filter: brightness(0.9);
    }
    
    .photo-preview-trigger img {
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .price-column {
        width: 130px;
        min-width: 130px;
        vertical-align: middle !important;
    }

    .price-value {
        display: inline-block;
        white-space: nowrap;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .backend-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.88);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 24px;
    }

    .backend-lightbox img {
        width: auto;
        max-width: min(78vw, 1250px);
        max-height: 82vh;
        display: block;
        border-radius: 12px;
        object-fit: contain;
        box-shadow: 0 24px 60px rgba(0,0,0,0.35);
    }

    .backend-lightbox-close {
        position: absolute;
        top: 18px;
        right: 22px;
        font-size: 32px;
        background: rgba(10,10,10,0.92);
        color: #e5e7eb;
        border: 2px solid rgba(82, 186, 255, 0.32);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 18px 30px rgba(0,0,0,0.28);
        line-height: 1;
    }

    .backend-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 54px;
        height: 54px;
        border: none;
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
        font-size: 34px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .backend-lightbox-nav.prev {
        left: 36px;
    }

    .backend-lightbox-nav.next {
        right: 36px;
    }

    @media (max-width: 768px) {
        .backend-lightbox {
            padding: 12px;
        }

        .backend-lightbox img {
            max-width: 100%;
            max-height: 72vh;
            border-radius: 10px;
        }

        .backend-lightbox-close {
            top: 12px;
            right: 12px;
            width: 50px;
            height: 50px;
            font-size: 28px;
        }

        .backend-lightbox-nav {
            width: 46px;
            height: 46px;
            font-size: 28px;
        }

        .backend-lightbox-nav.prev {
            left: 12px;
        }

        .backend-lightbox-nav.next {
            right: 12px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var photoTriggers = document.querySelectorAll('.photo-preview-trigger');
    var lightbox = document.getElementById('umkmPhotoLightbox');
    var previewImage = document.getElementById('umkmPhotoPreviewImage');
    var prevBtn = document.getElementById('umkmPhotoPrev');
    var nextBtn = document.getElementById('umkmPhotoNext');
    var currentPhotos = [];
    var currentIndex = 0;
    
    photoTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            currentPhotos = JSON.parse(this.getAttribute('data-photos') || '[]');
            currentIndex = 0;
            renderUmkmPreview();
            if (lightbox) {
                lightbox.style.display = 'flex';
            }
        });
    });

    function renderUmkmPreview() {
        if (!previewImage || currentPhotos.length === 0) {
            return;
        }

        previewImage.src = currentPhotos[currentIndex];

        if (prevBtn && nextBtn) {
            var shouldShowNav = currentPhotos.length > 1;
            prevBtn.classList.toggle('d-none', !shouldShowNav);
            nextBtn.classList.toggle('d-none', !shouldShowNav);
        }
    }

    window.closeUmkmPhotoPreview = function() {
        if (lightbox) {
            lightbox.style.display = 'none';
        }
    };

    window.nextUmkmPhoto = function() {
        if (currentPhotos.length < 2) return;
        currentIndex = (currentIndex + 1) % currentPhotos.length;
        renderUmkmPreview();
    };

    window.prevUmkmPhoto = function() {
        if (currentPhotos.length < 2) return;
        currentIndex = (currentIndex - 1 + currentPhotos.length) % currentPhotos.length;
        renderUmkmPreview();
    };
});
</script>

@endsection
