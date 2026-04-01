@extends('backend.main_dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Global Settings</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Global Settings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            @foreach($settings as $category => $items)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }} text-capitalize" 
                                   id="tab-{{ $category }}" data-toggle="pill" href="#content-{{ $category }}" 
                                   role="tab" aria-controls="content-{{ $category }}" aria-selected="true">
                                   {{ $category }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                @foreach($settings as $category => $items)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                     id="content-{{ $category }}" role="tabpanel" aria-labelledby="tab-{{ $category }}">
                                    
                                    @foreach($items as $setting)
                                    <div class="form-group row mb-4">
                                        <label class="col-sm-3 col-form-label">{{ $setting->label }}</label>
                                        <div class="col-sm-9">
                                            @if($setting->type == 'text')
                                                <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                            @elseif($setting->type == 'number')
                                                <input type="number" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                            @elseif($setting->type == 'textarea')
                                                <textarea name="{{ $setting->key }}" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                            @elseif($setting->type == 'checkbox')
                                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                    <input type="checkbox" name="{{ $setting->key }}" class="custom-control-input" 
                                                           id="switch-{{ $setting->key }}" {{ $setting->value == 'true' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch-{{ $setting->key }}">Enable / Disable</label>
                                                </div>
                                            @elseif($setting->type == 'file')
                                                @if($setting->value)
                                                    <div class="mb-3">
                                                        <img src="{{ storage_asset_url($setting->value) }}" alt="Preview" class="img-thumbnail" style="max-height: 100px; display: block;">
                                                        <a href="{{ route('settings.deleteLogo', $setting->id) }}"
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Yakin ingin menghapus logo ini?')">
                                                           <i class="fas fa-trash"></i> Delete Logo
                                                        </a>
                                                    </div>
                                                @endif
                                                <div class="custom-file">
                                                    <input type="file" name="{{ $setting->key }}" class="custom-file-input">
                                                    <label class="custom-file-label">Choose file</label>
                                                </div>
                                            @elseif($setting->type == 'json_channels')
                                                @php
                                                    $channels = json_decode($setting->value ?? '[]', true) ?? [];
                                                @endphp
                                                <div id="channels-wrapper">
                                                    @foreach($channels as $i => $ch)
                                                    <div class="card card-secondary card-outline mb-3 channel-item">
                                                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                                            <span class="font-weight-bold">Channel #{{ $i + 1 }}</span>
                                                            <button type="button" class="btn btn-danger btn-xs remove-channel">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </div>
                                                        <div class="card-body py-2">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-6">
                                                                    <label class="small">Nama Channel</label>
                                                                    <input type="text" name="channels_name[]" class="form-control form-control-sm"
                                                                        placeholder="cth: Transfer BCA, QRIS, VA Mandiri"
                                                                        value="{{ $ch['name'] ?? '' }}" required>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="small">Tipe</label>
                                                                    <select name="channels_type[]" class="form-control form-control-sm">
                                                                        <option value="bank_transfer" {{ ($ch['type'] ?? '') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                                        <option value="qris" {{ ($ch['type'] ?? '') === 'qris' ? 'selected' : '' }}>QRIS</option>
                                                                        <option value="va" {{ ($ch['type'] ?? '') === 'va' ? 'selected' : '' }}>Virtual Account</option>
                                                                        <option value="ewallet" {{ ($ch['type'] ?? '') === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                                                        <option value="other" {{ ($ch['type'] ?? '') === 'other' ? 'selected' : '' }}>Lainnya</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="small">Nomor Rekening / VA / ID</label>
                                                                    <input type="text" name="channels_account_number[]" class="form-control form-control-sm"
                                                                        placeholder="cth: 1234567890"
                                                                        value="{{ $ch['account_number'] ?? '' }}">
                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                    <label class="small">Atas Nama</label>
                                                                    <input type="text" name="channels_account_name[]" class="form-control form-control-sm"
                                                                        placeholder="cth: PT Agrowisata Bandung"
                                                                        value="{{ $ch['account_name'] ?? '' }}">
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label class="small">Instruksi Tambahan</label>
                                                                    <textarea name="channels_instructions[]" class="form-control form-control-sm" rows="2"
                                                                        placeholder="cth: Transfer sesuai total, lalu upload bukti.">{{ $ch['instructions'] ?? '' }}</textarea>
                                                                </div>
                                                                {{-- QR IMAGE UPLOAD (hanya untuk channel yang sudah tersimpan) --}}
                                                                <div class="form-group col-12 qr-upload-section" data-type="{{ $ch['type'] ?? '' }}">
                                                                    <label class="small">QR Code Image <span class="text-muted">(opsional, untuk QRIS / E-Wallet)</span></label>
                                                                    @if (!empty($ch['qr_image']))
                                                                        <div class="mb-2 d-flex align-items-center gap-2">
                                                                            <img src="{{ asset('storage/' . $ch['qr_image']) }}"
                                                                                alt="QR Code"
                                                                                style="max-height:100px; border:1px solid #ddd; border-radius:6px; padding:4px;">
                                                                            <form action="{{ route('settings.channel_qr_delete', $i) }}"
                                                                                method="POST" style="display:inline"
                                                                                onsubmit="return confirm('Hapus QR Code ini?')">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-danger btn-xs ml-2">
                                                                                    <i class="fas fa-trash"></i> Hapus QR
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                    <form action="{{ route('settings.channel_qr', $i) }}"
                                                                        method="POST" enctype="multipart/form-data"
                                                                        class="d-flex align-items-center gap-2">
                                                                        @csrf
                                                                        <input type="file" name="qr_image" accept="image/*"
                                                                            class="form-control form-control-sm" style="max-width:260px;">
                                                                        <button type="submit" class="btn btn-secondary btn-sm ml-2">
                                                                            <i class="fas fa-upload"></i> {{ empty($ch['qr_image']) ? 'Upload QR' : 'Ganti QR' }}
                                                                        </button>
                                                                    </form>
                                                                    <small class="text-muted">Format: JPG, PNG, WEBP. Maks 2MB.</small>
                                                                </div>
                                                                <div class="form-group col-12 mb-0">
                                                                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                                        <input type="checkbox" name="channels_is_active[{{ $i }}]"
                                                                            value="true"
                                                                            class="custom-control-input"
                                                                            id="ch-active-{{ $i }}"
                                                                            {{ ($ch['is_active'] ?? true) ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="ch-active-{{ $i }}">Aktif</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <button type="button" id="add-channel" class="btn btn-secondary btn-sm mt-1">
                                                    <i class="fas fa-plus"></i> Tambah Channel
                                                </button>
                                                <small class="d-block text-muted mt-2">Key: <code>{{ $setting->key }}</code></small>
                                            @endif
                                            @if($setting->type !== 'json_channels')
                                            <small class="text-muted">Key: <code>{{ $setting->key }}</code></small>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach

                                </div>
                                @endforeach
                            </div>
                            
                            <div class="card-footer bg-white border-top">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Tambah channel baru
        let channelCount = $('#channels-wrapper .channel-item').length;
        $('#add-channel').on('click', function () {
            const i = channelCount++;
            const html = `
            <div class="card card-secondary card-outline mb-3 channel-item">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <span class="font-weight-bold">Channel Baru</span>
                    <button type="button" class="btn btn-danger btn-xs remove-channel"><i class="fas fa-trash"></i> Hapus</button>
                </div>
                <div class="card-body py-2">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small">Nama Channel</label>
                            <input type="text" name="channels_name[]" class="form-control form-control-sm" placeholder="cth: Transfer BCA, QRIS, VA Mandiri" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small">Tipe</label>
                            <select name="channels_type[]" class="form-control form-control-sm">
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="va">Virtual Account</option>
                                <option value="ewallet">E-Wallet</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small">Nomor Rekening / VA / ID</label>
                            <input type="text" name="channels_account_number[]" class="form-control form-control-sm" placeholder="cth: 1234567890">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small">Atas Nama</label>
                            <input type="text" name="channels_account_name[]" class="form-control form-control-sm" placeholder="cth: PT Agrowisata Bandung">
                        </div>
                        <div class="form-group col-12">
                            <label class="small">Instruksi Tambahan</label>
                            <textarea name="channels_instructions[]" class="form-control form-control-sm" rows="2" placeholder="cth: Transfer sesuai total, lalu upload bukti."></textarea>
                        </div>
                        <div class="form-group col-12 mb-0">
                            <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                <input type="checkbox" name="channels_is_active[${i}]" value="true" class="custom-control-input" id="ch-active-${i}" checked>
                                <label class="custom-control-label" for="ch-active-${i}">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            $('#channels-wrapper').append(html);
        });

        // Hapus channel
        $(document).on('click', '.remove-channel', function () {
            $(this).closest('.channel-item').remove();
        });
    });
</script>
@endpush
