@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ isset($vendor) ? 'Edit Vendor' : 'Tambah Vendor' }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- ERROR VALIDATION --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Vendor</h3>
                </div>

                <form action="{{ isset($vendor) ? route('vendors.update', $vendor->id) : route('vendors.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($vendor))
                        @method('PUT')
                    @endif

                    <div class="card-body">

                        <!-- NAMA -->
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $vendor->name ?? '') }}"
                                placeholder="Contoh: Agro Lembang" required>
                        </div>

                        <!-- EMAIL -->
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $vendor->email ?? '') }}"
                                placeholder="example@gmail.com" required>
                        </div>

                        <!-- PHONE -->
                        <div class="form-group">
                            <label>No HP <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $vendor->phone ?? '') }}"
                                placeholder="+628xxxxxxxxxx"
                                required
                                inputmode="numeric">
                        </div>

                        <!-- AREA -->
                        <div class="form-group">
                            <label>Area <span class="text-danger">*</span></label>
                            <select name="area_id" class="form-control" required>
                                <option value="">-- Pilih Area --</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}"
                                        {{ old('area_id', $vendor->area_id ?? '') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- ADDRESS -->
                        <div class="form-group">
                            <label>Alamat <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="3" style="resize: none;"
                                placeholder="Masukkan alamat lengkap" required>{{ old('address', $vendor->address ?? '') }}</textarea>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="form-group">
                            <label>Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" style="resize: none;"
                                placeholder="Deskripsi vendor..." required>{{ old('description', $vendor->description ?? '') }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary" id="submitBtn">
                            Simpan
                        </button>

                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </section>

    <script>
        const phone = document.querySelector('[name="phone"]');
        const btn = document.getElementById('submitBtn');

        function formatPhone(input) {
            let value = input.value.replace(/[^0-9+]/g, '');

            if (value.startsWith('0')) {
                value = '+62' + value.substring(1);
            }

            if (value.startsWith('62') && !value.startsWith('+62')) {
                value = '+' + value;
            }

            input.value = value;
        }

        // khusus phone
        phone.addEventListener('input', function () {
            formatPhone(this);
        });
    </script>
@endsection
