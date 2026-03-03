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
                                <div id="preview-container" class="mt-3"></div>
@push('scripts')
<script>
function previewImages(event) {
    const files = event.target.files;
    const preview = document.getElementById('preview-container');
    preview.innerHTML = '';
    if (files) {
        Array.from(files).forEach(file => {
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '120px';
                    img.style.margin = '5px';
                    img.style.borderRadius = '8px';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}
</script>
@endpush

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
                                        <div style="display:flex; flex-wrap:wrap; gap:10px;">
                                            @foreach($allPhotos as $p)
                                                <div style="text-align:center;">
                                                    <img src="{{ asset('storage/' . $p->path_foto) }}" alt="Photo" style="max-width:120px; border-radius:8px; display:block; margin-bottom:4px;">
                                                    <label style="font-size:13px;">
                                                        <input type="checkbox" name="delete_photos[]" value="{{ $p->id }}"> Delete
                                                    </label>
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