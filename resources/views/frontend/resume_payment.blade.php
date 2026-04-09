@extends('frontend.main')

@section('content')
<section class="resume-payment-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <a href="{{ route('home') }}" class="btn btn-light rounded-circle p-0 resume-back-btn" aria-label="Beranda">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="font-display fs-5 fw-bold mb-0">Lanjutkan Pembayaran</h1>
                        <p class="text-muted small mb-0">Booking <strong>{{ $booking->booking_code }}</strong></p>
                    </div>
                </div>

                {{-- Status alert --}}
                @if (($payment->payment_method ?? null) === 'manual_transfer' && $payment->status === 'revision')
                    <div class="resume-alert resume-alert-warning mb-4">
                        <div class="resume-alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Bukti Transfer Perlu Direvisi</div>
                            @if ($payment->admin_note)
                                <div class="small mt-1 opacity-75">Catatan Admin: <em>{{ $payment->admin_note }}</em></div>
                            @endif
                            <div class="small mt-1 opacity-75">Silakan upload ulang bukti transfer yang benar.</div>
                        </div>
                    </div>
                @elseif (($payment->payment_method ?? null) === 'manual_transfer' && $payment->transfer_proof)
                    <div class="resume-alert resume-alert-info mb-4">
                        <div class="resume-alert-icon"><i class="bi bi-hourglass-split"></i></div>
                        <div>
                            <div class="fw-semibold">Menunggu Konfirmasi Admin</div>
                            <div class="small mt-1 opacity-75">Bukti transfer sudah diterima. Admin akan segera memverifikasi.</div>
                        </div>
                    </div>
                @endif

                {{-- Booking summary card --}}
                <div class="resume-summary-card mb-4">
                    <div class="resume-summary-row">
                        <span>Kode Booking</span>
                        <strong class="font-display">{{ $booking->booking_code }}</strong>
                    </div>
                    <div class="resume-summary-row">
                        <span>Nama Paket</span>
                        <strong class="text-end" style="max-width:55%;">{{ $booking->paketTour->nama_paket ?? '-' }}</strong>
                    </div>
                    <div class="resume-summary-row">
                        <span>Tanggal Kunjungan</span>
                        <strong>{{ \Carbon\Carbon::parse($booking->visit_date)->translatedFormat('d M Y') }}</strong>
                    </div>
                    <div class="resume-summary-row border-0 pb-0">
                        <span>Total Bayar</span>
                        <strong class="text-primary-agro font-display fs-5">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong>
                    </div>
                </div>

                @if (($payment->payment_method ?? null) === 'manual_transfer')
                    @php
                        $channels = collect(json_decode(get_setting('manual_payment_channels', '[]'), true) ?? [])
                            ->where('is_active', true)->values();
                        $selectedChannel = $payment->selected_channel
                            ? collect($channels)->firstWhere('name', $payment->selected_channel)
                            : null;
                    @endphp

                    {{-- Pilih channel --}}
                    @if (!$selectedChannel && $channels->isNotEmpty())
                        <div class="mb-4">
                            <p class="fw-semibold small mb-3">Pilih Metode Pembayaran:</p>
                            <form action="{{ route('frontend.select_payment_channel', $booking->booking_code) }}" method="POST">
                                @csrf
                                <div class="d-flex flex-column gap-2 mb-3">
                                    @foreach ($channels as $ch)
                                        <label class="resume-channel-option">
                                            <input type="radio" name="selected_channel" value="{{ $ch['name'] }}" required>
                                            <div class="resume-channel-body">
                                                <div class="resume-channel-icon">
                                                    <i class="bi bi-{{ ($ch['type'] ?? '') === 'qris' ? 'qr-code' : 'bank' }}"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold small">{{ $ch['name'] }}</div>
                                                    @if (!empty($ch['account_number']))
                                                        <div class="text-muted" style="font-size:0.78rem;">{{ $ch['account_number'] }}</div>
                                                    @endif
                                                </div>
                                                <span class="resume-channel-badge">{{ strtoupper($ch['type'] ?? 'bank') }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <button type="submit" class="btn btn-agro-primary w-100">
                                    <i class="bi bi-check-circle me-2"></i>Pilih & Lihat Instruksi
                                </button>
                            </form>
                        </div>

                    {{-- Instruksi channel terpilih --}}
                    @elseif ($selectedChannel && !$payment->transfer_proof)
                        <div class="resume-instruction-card mb-4">
                            <div class="resume-instruction-header">
                                <i class="bi bi-bank2"></i>
                                <span>Instruksi Pembayaran — {{ $selectedChannel['name'] }}</span>
                            </div>

                            @if (!empty($selectedChannel['qr_image']))
                                <div class="text-center my-3">
                                    <img src="{{ storage_asset_url($selectedChannel['qr_image']) }}"
                                         alt="QR Code {{ $selectedChannel['name'] }}"
                                         class="resume-qr-img">
                                    <p class="small text-muted mt-2 mb-0">Scan QR Code untuk membayar</p>
                                </div>
                            @endif

                            @if (!empty($selectedChannel['account_number']))
                                <div class="resume-instruction-row">
                                    <span class="resume-instruction-label">Nomor Rekening / VA / ID</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="resume-instruction-value" id="accNumber">{{ $selectedChannel['account_number'] }}</span>
                                        <button class="btn-copy-inline" onclick="copyText('accNumber')" title="Salin">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            @if (!empty($selectedChannel['account_name']))
                                <div class="resume-instruction-row">
                                    <span class="resume-instruction-label">Atas Nama</span>
                                    <span class="resume-instruction-value">{{ $selectedChannel['account_name'] }}</span>
                                </div>
                            @endif
                            @if (!empty($selectedChannel['instructions']))
                                <div class="resume-instruction-note mt-2">
                                    {!! nl2br(e($selectedChannel['instructions'])) !!}
                                </div>
                            @endif
                        </div>

                    {{-- Fallback --}}
                    @elseif (!$selectedChannel && !$payment->transfer_proof)
                        <div class="resume-instruction-card mb-4">
                            <div class="resume-instruction-header">
                                <i class="bi bi-bank2"></i>
                                <span>Instruksi Transfer Manual</span>
                            </div>
                            <div class="resume-instruction-row">
                                <span class="resume-instruction-label">Bank</span>
                                <span class="resume-instruction-value">{{ get_setting('manual_payment_bank_name', '-') }}</span>
                            </div>
                            <div class="resume-instruction-row">
                                <span class="resume-instruction-label">Nomor Rekening</span>
                                <span class="resume-instruction-value">{{ get_setting('manual_payment_account_number', '-') }}</span>
                            </div>
                            <div class="resume-instruction-row">
                                <span class="resume-instruction-label">Atas Nama</span>
                                <span class="resume-instruction-value">{{ get_setting('manual_payment_account_name', '-') }}</span>
                            </div>
                            @if(get_setting('manual_payment_instructions'))
                                <div class="resume-instruction-note mt-2">
                                    {!! nl2br(e(get_setting('manual_payment_instructions'))) !!}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Session success --}}
                    @if (session('success'))
                        <div class="resume-alert resume-alert-success mb-3">
                            <div class="resume-alert-icon"><i class="bi bi-check-circle-fill"></i></div>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    {{-- Bukti sudah diupload --}}
                    @if ($payment->transfer_proof && $payment->status !== 'revision')
                        <div class="resume-proof-preview mb-4">
                            <p class="fw-semibold small mb-2">Bukti Transfer Terkirim</p>
                            <img src="{{ storage_asset_url($payment->transfer_proof) }}"
                                 alt="Bukti Transfer"
                                 class="resume-proof-img">
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <i class="bi bi-hourglass-split text-primary-agro" style="font-size:0.85rem;"></i>
                                <span class="small text-muted">
                                    Diunggah {{ \Carbon\Carbon::parse($payment->transfer_proof_uploaded_at)->format('d M Y, H:i') }} — Sedang diverifikasi admin.
                                </span>
                            </div>
                        </div>

                    {{-- Form upload — hanya tampil kalau channel sudah dipilih (atau tidak ada channel) --}}
                    @elseif ($selectedChannel || $channels->isEmpty())
                        <form action="{{ route('frontend.upload_transfer_proof', $booking->booking_code) }}"
                              method="POST" enctype="multipart/form-data" class="mb-4" id="uploadProofForm">
                            @csrf
                            <p class="fw-semibold small mb-2">
                                {{ $payment->status === 'revision' ? 'Upload Ulang Bukti Transfer' : 'Upload Bukti Transfer' }}
                            </p>

                            <input type="file" id="proofFileInput" name="transfer_proof" accept="image/*"
                                   class="d-none @error('transfer_proof') is-invalid @enderror"
                                   onchange="previewProof(this)">

                            <button type="button" class="btn btn-outline-secondary w-100 mb-3"
                                    onclick="document.getElementById('proofFileInput').click()">
                                <i class="bi bi-image me-2"></i>Pilih Foto Bukti Transfer
                            </button>

                            @error('transfer_proof')
                                <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror

                            <div id="proofPreviewWrap" class="d-none mb-3">
                                <img id="proofPreviewImg" src="" alt="Preview"
                                     class="rounded-3 border w-100"
                                     style="max-height:200px; object-fit:contain; background:#f8f9f7;">
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span id="proofFileName" class="small text-muted text-truncate"></span>
                                    <button type="button" class="btn btn-sm btn-light"
                                            onclick="clearProof()" aria-label="Hapus foto">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-agro-primary w-100" id="btnUploadProof" disabled>
                                <i class="bi bi-send me-2"></i>
                                {{ $payment->status === 'revision' ? 'Kirim Ulang Bukti Transfer' : 'Kirim Bukti Transfer' }}
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Action buttons --}}
                <div class="d-flex flex-column gap-2 mt-2 mb-4">
                    @if (($payment->payment_method ?? null) === 'manual_transfer')
                        <a href="{{ route('frontend.invoice', $booking->booking_code) }}"
                           class="btn btn-outline-secondary w-100">
                            <i class="bi bi-file-earmark-text me-2"></i>Lihat Invoice & Status
                        </a>
                    @else
                        <button type="button" id="resumePayBtn" class="btn btn-agro-primary w-100">
                            <i class="bi bi-credit-card me-2"></i>Lanjutkan Bayar Sekarang
                        </button>
                    @endif
                    <a href="{{ route('home') }}" class="btn btn-light w-100">
                        <i class="bi bi-house me-2"></i>Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if (($payment->payment_method ?? null) !== 'manual_transfer')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var payBtn = document.getElementById('resumePayBtn');
    if (!payBtn) return;
    payBtn.addEventListener('click', function () {
        window.snap.pay(@json($payment->snap_token), {
            onSuccess: function () {
                localStorage.removeItem('last_pending_booking');
                window.location.href = @json(route('frontend.invoice', $booking->booking_code));
            },
            onPending: function () {
                localStorage.setItem('last_pending_booking', JSON.stringify({
                    booking_code: @json($booking->booking_code),
                    resume_url: @json(route('payment.resume', $booking->booking_code)),
                    saved_at: new Date().toISOString()
                }));
            },
            onError: function () { alert('Pembayaran gagal. Silakan coba lagi.'); },
            onClose: function () {
                localStorage.setItem('last_pending_booking', JSON.stringify({
                    booking_code: @json($booking->booking_code),
                    resume_url: @json(route('payment.resume', $booking->booking_code)),
                    saved_at: new Date().toISOString()
                }));
            }
        });
    });
});
</script>
@endif

<script>
function previewProof(input) {
    var file = input.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('proofPreviewImg').src = e.target.result;
        document.getElementById('proofFileName').textContent = file.name;
        document.getElementById('proofPreviewWrap').classList.remove('d-none');
        document.getElementById('btnUploadProof').disabled = false;
    };
    reader.readAsDataURL(file);
}

function clearProof() {
    var input = document.getElementById('proofFileInput');
    if (input) input.value = '';
    document.getElementById('proofPreviewImg').src = '';
    document.getElementById('proofFileName').textContent = '';
    document.getElementById('proofPreviewWrap').classList.add('d-none');
    document.getElementById('btnUploadProof').disabled = true;
}

function copyText(elId) {
    var el = document.getElementById(elId);
    if (!el) return;
    navigator.clipboard ? navigator.clipboard.writeText(el.textContent.trim()) : (function(){
        var t = document.createElement('textarea');
        t.value = el.textContent.trim();
        document.body.appendChild(t); t.select(); document.execCommand('copy'); document.body.removeChild(t);
    })();
    var btn = event.currentTarget;
    var icon = btn.querySelector('i');
    if (icon) { icon.className = 'bi bi-check-lg text-success'; setTimeout(function(){ icon.className = 'bi bi-clipboard'; }, 2000); }
}
</script>

<style>
/* ===== RESUME PAYMENT ===== */
.resume-payment-section { min-height: 80vh; background: var(--agro-bg); }

.resume-back-btn {
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* Alerts */
.resume-alert {
    display: flex; align-items: flex-start; gap: 0.875rem;
    padding: 1rem 1.1rem; border-radius: 12px; font-size: 0.875rem;
}
.resume-alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
.resume-alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
.resume-alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.resume-alert-icon { font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }

/* Summary card */
.resume-summary-card {
    background: #fff;
    border: 1px solid var(--agro-border);
    border-radius: 14px;
    overflow: hidden;
}
.resume-summary-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.75rem 1.1rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-size: 0.875rem;
    gap: 1rem;
}
.resume-summary-row span { color: var(--agro-text-muted); flex-shrink: 0; }

/* Channel selector */
.resume-channel-option {
    display: block; cursor: pointer;
    border: 1.5px solid var(--agro-border);
    border-radius: 12px; overflow: hidden;
    transition: border-color 0.2s ease;
}
.resume-channel-option:has(input:checked) { border-color: var(--agro-primary); background: rgba(45,106,79,0.03); }
.resume-channel-option input { position: absolute; opacity: 0; pointer-events: none; }
.resume-channel-body {
    display: flex; align-items: center; gap: 0.875rem;
    padding: 0.875rem 1rem;
}
.resume-channel-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 10px; background: rgba(45,106,79,0.08);
    color: var(--agro-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.resume-channel-badge {
    margin-left: auto; flex-shrink: 0;
    background: rgba(45,106,79,0.1); color: var(--agro-primary);
    font-size: 0.65rem; font-weight: 700;
    padding: 2px 8px; border-radius: 50rem;
    letter-spacing: 0.05em;
}

/* Instruction card */
.resume-instruction-card {
    background: #fff;
    border: 1.5px solid rgba(45,106,79,0.2);
    border-radius: 14px;
    overflow: hidden;
}
.resume-instruction-header {
    display: flex; align-items: center; gap: 0.6rem;
    background: rgba(45,106,79,0.06);
    padding: 0.75rem 1.1rem;
    font-weight: 700; font-size: 0.875rem;
    color: var(--agro-primary);
    border-bottom: 1px solid rgba(45,106,79,0.1);
}
.resume-instruction-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 0.65rem 1.1rem;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    gap: 1rem;
}
.resume-instruction-label { font-size: 0.78rem; color: var(--agro-text-muted); flex-shrink: 0; }
.resume-instruction-value { font-weight: 700; font-size: 0.9rem; color: var(--agro-text); }
.resume-instruction-note {
    padding: 0.65rem 1.1rem 0.875rem;
    font-size: 0.8rem; color: var(--agro-text-muted); line-height: 1.6;
}
.resume-qr-img {
    max-width: 180px; border: 1px solid #ddd;
    border-radius: 10px; padding: 8px; background: #fff;
}

/* Copy inline button */
.btn-copy-inline {
    background: none; border: none; padding: 2px 6px;
    color: var(--agro-text-muted); cursor: pointer; border-radius: 6px;
    transition: color 0.2s, background 0.2s;
}
.btn-copy-inline:hover { color: var(--agro-primary); background: rgba(45,106,79,0.08); }

/* File drop — removed, using button instead */

/* Proof preview (uploaded) */
.resume-proof-preview {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
}
.resume-proof-img {
    max-height: 200px; max-width: 100%;
    border-radius: 8px; object-fit: contain;
    border: 1px solid #ddd;
}

@media (max-width: 575.98px) {
    .resume-summary-row { font-size: 0.82rem; }
    .resume-instruction-card { border-radius: 12px; }
}
</style>
@endpush
