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
                                                <div class="d-flex flex-wrap align-items-start justify-content-center" style="gap: 12px;">
                                                    @foreach($paket->photos as $photo)
                                                        <img src="{{ Storage::url($photo->path_foto) }}" alt="Photo" class="preview-image" data-image="{{ Storage::url($photo->path_foto) }}" style="max-width:90px; border-radius:8px; margin-bottom:4px; cursor:pointer; transition:0.3s;">
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
            Swal.fire({
                imageUrl: this.dataset.image,
                imageAlt: 'Preview Photo',
                showConfirmButton: false,
                showCloseButton: true,
                width: 900,
                background: '#ffffff',
                backdrop: 'rgba(0,0,0,0.85)'
            });
        });
    });
</script>

@endsection