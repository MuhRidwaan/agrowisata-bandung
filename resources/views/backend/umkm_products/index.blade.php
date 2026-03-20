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
                            <th class="text-right">Price</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($umkmProducts as $product)
                            <tr>
                                <td>{{ $loop->iteration + ($umkmProducts->currentPage() - 1) * $umkmProducts->perPage() }}</td>

                                {{-- Photo --}}
                                <td class="text-center">
                                    @if($product->photos->count() > 0)
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;">
                                            @foreach($product->photos->take(3) as $photo)
                                                <a href="#" 
                                                   class="photo-preview-trigger"
                                                   data-toggle="modal" 
                                                   data-target="#photoModal"
                                                   data-product-name="{{ $product->name }}"
                                                   data-photos="{{ json_encode($product->photos->pluck('path_foto')->map(fn($p) => asset('storage/' . $p))->toArray()) }}"
                                                   style="display: inline-block;">
                                                    <img src="{{ asset('storage/' . $photo->path_foto) }}" 
                                                         alt="Photo" 
                                                         style="width: 48px; height: 48px; object-fit: cover; border-radius: 4px; cursor: pointer; border: 1px solid #ddd; display: block;"
                                                         class="img-hover">
                                                </a>
                                            @endforeach
                                            @if($product->photos->count() > 3)
                                                <div style="width: 48px; height: 48px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 1px solid #ddd;">
                                                    <small><strong>+{{ $product->photos->count() - 3 }}</strong></small>
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
                                <td class="text-right">
                                    @if($product->price)
                                        <strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
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

{{-- Photo Preview Modal --}}
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Photo Preview - <span id="productName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="photoCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" id="carouselInner">
                        <!-- Photos akan di-inject via JavaScript -->
                    </div>
                    <a class="carousel-control-prev" href="#photoCarousel" role="button" data-slide="prev" style="display:none;" id="prevBtn">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#photoCarousel" role="button" data-slide="next" style="display:none;" id="nextBtn">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
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
    
    .carousel-inner img {
        max-height: 500px;
        object-fit: contain;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Photo preview modal handler
    var photoTriggers = document.querySelectorAll('.photo-preview-trigger');
    
    photoTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            
            var productName = this.getAttribute('data-product-name');
            var photos = JSON.parse(this.getAttribute('data-photos'));
            
            // Update modal title
            document.getElementById('productName').textContent = productName;
            
            // Build carousel
            var carouselInner = document.getElementById('carouselInner');
            carouselInner.innerHTML = '';
            
            photos.forEach(function(photoUrl, index) {
                var isActive = index === 0 ? 'active' : '';
                var htmlContent = `
                    <div class="carousel-item ${isActive}">
                        <img src="${photoUrl}" class="d-block w-100" alt="Photo ${index + 1}">
                        <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 5px;">
                            <small>Photo ${index + 1} of ${photos.length}</small>
                        </div>
                    </div>
                `;
                carouselInner.innerHTML += htmlContent;
            });
            
            // Show/hide carousel controls based on photo count
            if (photos.length > 1) {
                document.getElementById('prevBtn').style.display = 'block';
                document.getElementById('nextBtn').style.display = 'block';
            } else {
                document.getElementById('prevBtn').style.display = 'none';
                document.getElementById('nextBtn').style.display = 'none';
            }
            
            // Restart carousel
            $('#photoCarousel').carousel('pause').carousel(0);
        });
    });
});
</script>

@endsection
