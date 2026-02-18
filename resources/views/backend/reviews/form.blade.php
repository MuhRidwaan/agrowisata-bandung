@extends('backend.main_dashboard')

@section('content')
    <!-- CONTENT HEADER -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                    <h1>Tambah Review</h1>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('vendors.index') }}">Vendor</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Review
                        </li>
                    </ol>
                </div>

            </div>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">

                    <div class="card card-warning">

                        <div class="card-header">
                            <h3 class="card-title">Form Review & Rating</h3>
                        </div>

                        <form action="{{ route('review.store') }}" method="POST">
                            @csrf

                            <div class="card-body">


                                <!-- PILIH VENDOR -->
                                <div class="form-group">
                                    <label>Vendor <span class="text-danger">*</span></label>
                                    <select name="vendor_id" class="form-control" required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- NAMA USER -->
                                <div class="form-group">
                                    <label>Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Masukkan nama kamu" required>
                                </div>

                                <!-- RATING -->
                                <div class="form-group">
                                    <label>Rating <span class="text-danger">*</span></label>
                                    <select name="rating" class="form-control" required>
                                        <option value="">-- Pilih Rating --</option>
                                        <option value="1">⭐ 1</option>
                                        <option value="2">⭐⭐ 2</option>
                                        <option value="3">⭐⭐⭐ 3</option>
                                        <option value="4">⭐⭐⭐⭐ 4</option>
                                        <option value="5">⭐⭐⭐⭐⭐ 5</option>
                                    </select>
                                </div>

                                <!-- KOMENTAR -->
                                <div class="form-group">
                                    <label>Komentar <span class="text-danger">*</span></label>
                                    <textarea name="comment" class="form-control" rows="6" style="resize: none;" overflow: hidden;
                                        placeholder="Tulis review kamu" required></textarea>
                                </div>

                            </div>

                            <div class="card-footer">
                                <button class="btn btn-warning" id="submitBtn" disabled>
                                    Kirim Review
                                </button>

                                <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- SCRIPT VALIDASI -->
    <script>
        const vendor = document.querySelector('[name="vendor_id"]');
        const nama = document.querySelector('[name="name"]');
        const rating = document.querySelector('[name="rating"]');
        const komentar = document.querySelector('[name="comment"]');
        const btn = document.getElementById('submitBtn');

        function checkForm() {
            if (
                vendor.value &&
                nama.value.trim() !== "" &&
                rating.value &&
                komentar.value.trim() !== ""
            ) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }

        vendor.addEventListener('change', checkForm);
        nama.addEventListener('input', checkForm);
        rating.addEventListener('change', checkForm);
        komentar.addEventListener('input', checkForm);
    </script>
@endsection
