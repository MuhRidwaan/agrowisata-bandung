@extends('backend.main_dashboard')

@section('content')

{{-- ================= CONTENT HEADER ================= --}}
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>Photo Gallery</h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Photo Gallery</li>
                </ol>
            </div>

        </div>
    </div>
</section>


{{-- ================= MAIN CONTENT ================= --}}
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">

                    {{-- CARD HEADER --}}
            <div class="card-header">
                <div class="row align-items-center">

                    <div class="col-md-6">
                        <h3 class="card-title mb-0">Photo Data</h3>
                    </div>

                    <div class="col-md-6 text-md-right text-left mt-2 mt-md-0">
                        <a href="{{ route('paket-tour-photos.create') }}"
                        class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add Photo
                        </a>
                    </div>

                </div>
            </div>

                    {{-- CARD BODY --}}
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center align-middle">

                            <thead class="thead">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tour Package</th>
                                    <th width="40%">Preview</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($pakets as $key => $paket)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $paket->nama_paket }}</td>
                                        <td>
                                            @if($paket->photos->count())
                                                <div class="photo-preview-grid">
                                                    @foreach($paket->photos as $photo)
                                                        <div class="photo-preview-card">
                                                            <img src="{{ $photo->photo_url }}"
                                                                alt="Photo"
                                                                class="preview-image photo-preview-thumb"
                                                                data-image="{{ $photo->photo_url }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($paket->photos->count())
                                                <a href="{{ route('paket-tour-photos.edit', $paket->photos->first()->id) }}" class="btn btn-warning btn-sm mr-1" title="Edit/Delete Photos">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('paket-tour-photos.create') }}" class="btn btn-warning btn-sm mr-1" title="Add Photos">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('paket-tour-photos.delete-by-paket', $paket->id) }}" method="POST" class="d-inline-block form-delete" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete All Photos">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No photo data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</section>

<div id="photoPreviewLightbox" class="review-lightbox" onclick="closePhotoPreview()">
    <button class="review-close photo-preview-close" onclick="event.stopPropagation(); closePhotoPreview()">✕</button>
    <img id="photoPreviewImage" onclick="event.stopPropagation()">
</div>


{{-- ================= SUCCESS ALERT ================= --}}
@if (session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2000,
        showConfirmButton: false
    });
</script>
@endif


{{-- ================= DELETE CONFIRMATION ================= --}}
<script>
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Deleted data cannot be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>


{{-- ================= IMAGE PREVIEW MODAL ================= --}}
<script>
    document.querySelectorAll('.preview-image').forEach(image => {
        image.addEventListener('click', function () {
            openPhotoPreview(this.dataset.image);
        });
    });

    function openPhotoPreview(src) {
        const lightbox = document.getElementById('photoPreviewLightbox');
        const image = document.getElementById('photoPreviewImage');

        if (!lightbox || !image) {
            return;
        }

        image.src = src;
        lightbox.style.display = 'flex';
    }

    function closePhotoPreview() {
        const lightbox = document.getElementById('photoPreviewLightbox');
        if (lightbox) {
            lightbox.style.display = 'none';
        }
    }
</script>

<style>
.photo-preview-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
}

.photo-preview-card {
    width: 118px;
}

.photo-preview-thumb {
    width: 118px;
    height: 148px;
    object-fit: cover;
    object-position: center;
    border-radius: 14px;
    cursor: pointer;
    border: 1px solid #dfe5eb;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #f8f9fa;
}

.photo-preview-thumb:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
}

.review-lightbox{
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.88);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 24px;
}

.review-lightbox img{
    width: auto;
    max-width: min(78vw, 1250px);
    max-height: 82vh;
    display: block;
    border-radius: 12px;
    object-fit: contain;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35);
}

.review-close{
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
}

.photo-preview-close{
    line-height: 1;
}

@media (max-width: 768px) {
    .photo-preview-grid {
        gap: 12px;
    }

    .photo-preview-card {
        width: 96px;
    }

    .photo-preview-thumb {
        width: 96px;
        height: 120px;
        border-radius: 12px;
    }

    .review-lightbox{
        padding: 12px;
    }

    .review-lightbox img{
        max-width: 100%;
        max-height: 72vh;
        border-radius: 10px;
    }

    .review-close{
        top: 12px;
        right: 12px;
        width: 50px;
        height: 50px;
        font-size: 28px;
    }
}

</style>

@endsection
