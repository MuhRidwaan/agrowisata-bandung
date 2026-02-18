@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">

        <div class="mb-3">
            <h4 class="mb-0">Upload Gallery Foto</h4>
            <small class="text-muted">Tambahkan beberapa foto untuk satu paket wisata</small>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('tour-galleries.store') }}" enctype="multipart/form-data">

                    @csrf

                    {{-- PILIH PAKET --}}
                    <div class="mb-4">
                        <label class="form-label">Paket Tour</label>
                        <select name="tour_package_id" class="form-control" required>
                            <option value="">-- Pilih Paket --</option>
                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}" {{ old('tour_package_id') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- INPUT FOTO & CAPTION --}}
                    <div class="mb-3">
                        <label class="form-label">Upload Foto & Caption</label>
                        <div id="gallery-upload-group">
                            <div class="input-group mb-2">
                                <input type="file" name="images[]" class="form-control" accept="image/*" required>
                                <input type="text" name="captions[]" class="form-control"
                                    placeholder="Caption foto (opsional)">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addGalleryInput()">Tambah
                            Foto</button>
                        <small class="text-muted d-block mt-1">
                            Bisa upload lebih dari satu gambar (jpg, png, jpeg — max 2MB) dan caption berbeda.
                        </small>
                    </div>
                    <script>
                        function addGalleryInput() {
                            const group = document.getElementById('gallery-upload-group');
                            const div = document.createElement('div');
                            div.className = 'input-group mb-2';
                            div.innerHTML =
                                `<input type="file" name="images[]" class="form-control" accept="image/*" required>
                        <input type="text" name="captions[]" class="form-control" placeholder="Caption foto (opsional)">
                        <button type=\"button\" class=\"btn btn-danger\" onclick=\"this.parentNode.remove()\">Hapus</button>`;
                            group.appendChild(div);
                        }
                    </script>

                    <hr>

                    <div class="d-flex gap-2">
                        <button class="btn btn-success">
                            Upload Foto
                        </button>

                        <a href="{{ route('tour-galleries.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>

    {{-- Preview Script --}}
    <script>
        document.getElementById('images').addEventListener('change', function(e) {
            const preview = document.getElementById('preview-container');
            preview.innerHTML = '';

            [...e.target.files].forEach(file => {
                const reader = new FileReader();

                reader.onload = function(event) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-3';

                    col.innerHTML = `
                <div class="card">
                    <img src="${event.target.result}" class="card-img-top" style="height:180px;object-fit:cover;">
                    <div class="card-body p-2 text-center">
                        <small class="text-muted">${file.name}</small>
                    </div>
                </div>
            `;

                    preview.appendChild(col);
                }

                reader.readAsDataURL(file);
            });
        });
    </script>

@endsection
