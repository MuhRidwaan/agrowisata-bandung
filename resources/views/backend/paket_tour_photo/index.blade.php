@extends('backend.main_dashboard')

@section('content')

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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">

                    {{-- HEADER --}}
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">

                            <h3 class="card-title mb-2">Photo Data</h3>

                            <a href="{{ route('paket-tour-photos.create') }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Photo
                            </a>

                        </div>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">

                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tour Package</th>
                                    <th width="25%">Preview</th>
                                    <th width="20%">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($photos as $key => $photo)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>
                                            {{ $photo->paketTour->nama_paket ?? '-' }}
                                        </td>

                                        <td>
                                            @if($photo->path_foto)
                                                <img src="{{ Storage::url($photo->path_foto) }}"
                                                     alt="Photo"
                                                     style="max-width:120px; border-radius:6px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>

                                        <td>

                                            {{-- EDIT --}}
                                            <a href="{{ route('paket-tour-photos.edit', $photo->id) }}"
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- DELETE --}}
                                            <form action="{{ route('paket-tour-photos.destroy', $photo->id) }}"
                                                  method="POST"
                                                  style="display:inline-block"
                                                  class="form-delete">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            No photo data available yet.
                                        </td>
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

{{-- Success Alert --}}
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

{{-- Delete Confirmation --}}
<script>
    document.querySelectorAll('.form-delete').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure you want to delete?',
                text: "Deleted data cannot be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

@endsection