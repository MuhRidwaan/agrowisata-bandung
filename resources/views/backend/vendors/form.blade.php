@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <h1>{{ isset($vendor) ? 'Edit Vendor' : 'Add Vendor' }}</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- VALIDATION ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Vendor Form</h3>
                </div>

                <form action="{{ isset($vendor) ? route('vendors.update', $vendor->id) : route('vendors.store') }}"
                    method="POST">
                    @csrf
                    @if (isset($vendor))
                        @method('PUT')
                    @endif

                    <div class="card-body">

                        <!-- NAME -->
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $vendor->name ?? '') }}"
                                placeholder="Example: Agro Lembang" required>
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
                            <label>Phone Number <span class="text-danger">*</span></label>
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
                                <option value="">-- Select Area --</option>
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
                            <label>Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="3" style="resize: none;"
                                placeholder="Enter full address" required>{{ old('address', $vendor->address ?? '') }}</textarea>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="form-group">
                            <label>Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" style="resize: none;"
                                placeholder="Vendor description..." required>{{ old('description', $vendor->description ?? '') }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary" id="submitBtn">
                            Save
                        </button>

                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                            Back
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </section>

    <script>
        const phone = document.querySelector('[name="phone"]');

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

        phone.addEventListener('input', function () {
            formatPhone(this);
        });
    </script>
@endsection