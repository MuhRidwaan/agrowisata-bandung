@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <h1>Setting WhatsApp</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form WhatsApp</h3>
                </div>

                <form action="{{ route('whatsappsetting.store') }}" method="POST">
                    @csrf

                    <div class="card-body">

                        <!-- NOMOR WA -->
                        <div class="form-group">
                            <label>Nomor WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" class="form-control" placeholder="+628xxxx" required
                                inputmode="numeric">
                        </div>

                        <!-- TEMPLATE PESAN -->
                        <div class="form-group">
                            <label>Template Pesan <span class="text-danger">*</span></label>
                            <textarea name="message_template" class="form-control" rows="5" style="resize: none;"
                                placeholder="Halo saya ingin tanya tentang @{{ nama }}" required></textarea>
                        </div>

                        <!-- STATUS -->
                        <div class="form-group">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="1">Aktif</option>
                                <option value="0">Non Aktif</option>
                            </select>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary" id="submitBtn" disabled>
                            Simpan
                        </button>

                        <a href="{{ route('whatsappsetting.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                </form>

            </div>

        </div>
    </section>

    <script>
        const phone = document.querySelector('[name="phone_number"]');
        const message = document.querySelector('[name="message_template"]');
        const status = document.querySelector('[name="is_active"]');
        const btn = document.getElementById('submitBtn');

        function formatPhone(input) {
            let value = input.value.replace(/[^0-9+]/g, '');

            // 0 → +62
            if (value.startsWith('0')) {
                value = '+62' + value.substring(1);
            }

            // 62 → +62
            if (value.startsWith('62') && !value.startsWith('+62')) {
                value = '+' + value;
            }

            input.value = value;

            checkForm();
        }

        function checkForm() {
            if (
                phone.value.trim() !== "" &&
                message.value.trim() !== "" &&
                status.value !== ""
            ) {
                btn.disabled = false;
            } else {
                btn.disabled = true;
            }
        }

        // event
        phone.addEventListener('input', function() {
            formatPhone(this);
        });

        message.addEventListener('input', checkForm);
        status.addEventListener('change', checkForm);

        // run awal
        checkForm();
    </script>
@endsection
