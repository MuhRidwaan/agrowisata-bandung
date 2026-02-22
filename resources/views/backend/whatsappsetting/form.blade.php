@extends('backend.main_dashboard')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <h1>{{ isset($setting) ? 'Edit WhatsApp Setting' : 'Add WhatsApp Setting' }}</h1>
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
                <h3 class="card-title">WhatsApp Form</h3>
            </div>

            <form action="{{ isset($setting) ? route('whatsappsetting.update', $setting->id) : route('whatsappsetting.store') }}"
                method="POST">
                @csrf
                @if (isset($setting))
                    @method('PUT')
                @endif

                <div class="card-body">

                    <!-- VENDOR -->
                    <div class="form-group">
                        <label>Vendor <span class="text-danger">*</span></label>
                        <select name="vendor_id" id="vendorSelect" class="form-control" required>
                            <option value="">-- Select Vendor --</option>

                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}"
                                    data-phone="{{ $vendor->phone }}"
                                    data-name="{{ $vendor->name }}"
                                    {{ old('vendor_id', $setting->vendor_id ?? '') == $vendor->id ? 'selected' : '' }}>
                                    
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- PHONE AUTO -->
                    <div class="form-group">
                        <label>WhatsApp Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" id="phoneInput"
                            value="{{ old('phone_number', $setting->phone_number ?? '') }}"
                            class="form-control" readonly required>
                    </div>

                    <!-- TEMPLATE -->
                    <div class="form-group">
                        <label>Message Template <span class="text-danger">*</span></label>
                        <select name="message_template" class="form-control" required>
                            <option value="">-- Select Template --</option>

                            @foreach ($templates as $template)
                                <option value="{{ $template }}"
                                    {{ old('message_template', $setting->message_template ?? '') == $template ? 'selected' : '' }}>
                                    {{ $template }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="card-footer">
                    <button class="btn btn-primary">
                        Save
                    </button>

                    <a href="{{ route('whatsappsetting.index') }}" class="btn btn-secondary">
                        Back
                    </a>
                </div>

            </form>

        </div>

    </div>
</section>

<script>
    const vendorSelect = document.getElementById('vendorSelect');
    const phoneInput = document.getElementById('phoneInput');

    function formatPhone(phone) {
        if (phone.startsWith('0')) {
            return '+62' + phone.substring(1);
        }
        if (phone.startsWith('62') && !phone.startsWith('+62')) {
            return '+' + phone;
        }
        return phone;
    }

    vendorSelect.addEventListener('change', function () {
        let selected = this.options[this.selectedIndex];
        let phone = selected.getAttribute('data-phone');

        if (phone) {
            phoneInput.value = formatPhone(phone);
        }
    });

    // AUTO LOAD ON EDIT
    window.addEventListener('load', function () {
        let selected = vendorSelect.options[vendorSelect.selectedIndex];
        let phone = selected.getAttribute('data-phone');

        if (phone && !phoneInput.value) {
            phoneInput.value = formatPhone(phone);
        }
    });
</script>

@endsection