@extends('frontend.main')

@section('content')
<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h4 fw-bold mb-2">Lanjutkan Pembayaran</h1>
                        <p class="text-muted mb-4">Silakan lanjutkan pembayaran booking Anda tanpa isi ulang data.</p>

                        {{-- STATUS BADGE --}}
                        @if (($payment->payment_method ?? null) === 'manual_transfer' && $payment->status === 'revision')
                            <div class="alert alert-warning border-0 rounded-3 mb-4 d-flex align-items-start gap-2">
                                <i class="fas fa-redo mt-1"></i>
                                <div>
                                    <strong>Bukti Transfer Perlu Direvisi</strong>
                                    @if ($payment->admin_note)
                                        <div class="small mt-1">Catatan Admin: <em>{{ $payment->admin_note }}</em></div>
                                    @endif
                                    <div class="small text-muted mt-1">Silakan upload ulang bukti transfer yang benar.</div>
                                </div>
                            </div>
                        @elseif (($payment->payment_method ?? null) === 'manual_transfer' && $payment->transfer_proof)
                            <div class="alert alert-info border-0 rounded-3 mb-4 d-flex align-items-center gap-2">
                                <i class="fas fa-hourglass-half"></i>
                                <div>
                                    <strong>Menunggu Konfirmasi Admin</strong>
                                    <div class="small">Bukti transfer sudah diterima. Admin akan segera memverifikasi pembayaran Anda.</div>
                                </div>
                            </div>
                        @endif

                        <div class="bg-light rounded-3 p-3 mb-4">
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Kode Booking</span>
                                <strong>{{ $booking->booking_code }}</strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Nama Paket</span>
                                <strong>{{ $booking->paketTour->nama_paket ?? '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">Tanggal Kunjungan</span>
                                <strong>{{ \Carbon\Carbon::parse($booking->visit_date)->format('d M Y') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Total Bayar</span>
                                <strong>Rp{{ number_format($booking->total_price, 0, ',', '.') }}</strong>
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

                            @if ($selectedChannel)
                                {{-- Sudah pilih channel, tampilkan detail HANYA jika belum upload bukti --}}
                                @if (!$payment->transfer_proof)
                                    <div class="alert alert-warning border-0 rounded-3 mb-4">
                                        <h2 class="h6 fw-bold mb-2">Instruksi Pembayaran — {{ $selectedChannel['name'] }}</h2>

                                        @if (!empty($selectedChannel['qr_image']))
                                            <div class="text-center my-3">
                                                <img src="{{ storage_asset_url($selectedChannel['qr_image']) }}"
                                                    alt="QR Code {{ $selectedChannel['name'] }}"
                                                    style="max-width: 200px; border: 1px solid #ddd; border-radius: 8px; padding: 8px; background:#fff;">
                                                <p class="small text-muted mt-1">Scan QR Code di atas untuk membayar</p>
                                            </div>
                                        @endif

                                        @if (!empty($selectedChannel['account_number']))
                                            <div class="small text-muted mb-1">Nomor Rekening / VA / ID</div>
                                            <div class="fw-semibold mb-2">{{ $selectedChannel['account_number'] }}</div>
                                        @endif
                                        @if (!empty($selectedChannel['account_name']))
                                            <div class="small text-muted mb-1">Atas Nama</div>
                                            <div class="fw-semibold mb-2">{{ $selectedChannel['account_name'] }}</div>
                                        @endif
                                        @if (!empty($selectedChannel['instructions']))
                                            <div class="small text-muted">{!! nl2br(e($selectedChannel['instructions'])) !!}</div>
                                        @endif
                                    </div>
                                @endif
                            @elseif ($channels->isNotEmpty())
                                {{-- Belum pilih channel, tampilkan pilihan --}}
                                <div class="mb-4">
                                    <p class="fw-semibold small mb-2">Pilih Metode Pembayaran Manual:</p>
                                    <form action="{{ route('frontend.select_payment_channel', $booking->booking_code) }}"
                                        method="POST">
                                        @csrf
                                        @foreach ($channels as $ch)
                                            <label class="d-block border rounded-3 p-3 mb-2 cursor-pointer"
                                                style="cursor:pointer;">
                                                <input type="radio" name="selected_channel" value="{{ $ch['name'] }}" required>
                                                <span class="fw-semibold ms-1">{{ $ch['name'] }}</span>
                                                @if (!empty($ch['account_number']))
                                                    <span class="text-muted small ms-2">— {{ $ch['account_number'] }}</span>
                                                @endif
                                                <span class="badge badge-secondary ms-1 text-uppercase" style="font-size:0.7rem;">{{ $ch['type'] }}</span>
                                            </label>
                                        @endforeach
                                        <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold rounded-3 mt-2">
                                            Pilih & Lihat Instruksi
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- Fallback ke setting lama --}}
                                @if (!$payment->transfer_proof)
                                    <div class="alert alert-warning border-0 rounded-3 mb-4">
                                        <h2 class="h6 fw-bold mb-2">Instruksi Transfer Manual</h2>
                                        <div class="small text-muted mb-1">Bank Tujuan</div>
                                        <div class="fw-semibold mb-2">{{ get_setting('manual_payment_bank_name', 'Transfer Bank') }}</div>
                                        <div class="small text-muted mb-1">Nomor Rekening</div>
                                        <div class="fw-semibold mb-2">{{ get_setting('manual_payment_account_number', '-') }}</div>
                                        <div class="small text-muted mb-1">Atas Nama</div>
                                        <div class="fw-semibold mb-3">{{ get_setting('manual_payment_account_name', '-') }}</div>
                                        <div class="small text-muted">{!! nl2br(e(get_setting('manual_payment_instructions', ''))) !!}</div>
                                    </div>
                                @endif
                            @endif

                            {{-- UPLOAD BUKTI TRANSFER --}}
                            @if (session('success'))
                                <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
                            @endif

                            @if ($payment->transfer_proof && $payment->status !== 'revision')
                                <div class="mb-4">
                                    <p class="small text-muted mb-1">Bukti transfer yang sudah diunggah:</p>
                                    <img src="{{ storage_asset_url($payment->transfer_proof) }}"
                                        alt="Bukti Transfer"
                                        class="img-fluid rounded-3 border"
                                        style="max-height: 220px; object-fit: contain;">
                                    <p class="small text-muted mt-1">
                                        Diunggah: {{ \Carbon\Carbon::parse($payment->transfer_proof_uploaded_at)->format('d M Y, H:i') }}
                                    </p>
                                    <p class="small text-info mb-0"><i class="fas fa-hourglass-half"></i> Sedang diverifikasi oleh admin.</p>
                                </div>
                            @else
                                <form action="{{ route('frontend.upload_transfer_proof', $booking->booking_code) }}"
                                    method="POST" enctype="multipart/form-data" class="mb-4">
                                    @csrf
                                    <label class="form-label fw-semibold small">
                                        {{ $payment->status === 'revision' ? 'Upload Ulang Bukti Transfer' : 'Upload Bukti Transfer' }}
                                    </label>
                                    <input type="file" name="transfer_proof" accept="image/*"
                                        class="form-control rounded-3 @error('transfer_proof') is-invalid @enderror">
                                    @error('transfer_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <p class="small text-muted mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
                                    <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold rounded-3 mt-1">
                                        <i class="fas fa-upload me-1"></i>
                                        {{ $payment->status === 'revision' ? 'Kirim Ulang Bukti Transfer' : 'Kirim Bukti Transfer' }}
                                    </button>
                                </form>
                            @endif
                        @endif

                        <div class="mt-4 mb-5">
                            <div class="row g-3">
                                @if (($payment->payment_method ?? null) === 'manual_transfer')
                                    <div class="col-12">
                                        <a href="{{ route('frontend.invoice', $booking->booking_code) }}"
                                            class="btn btn-outline-secondary w-100 py-3 fw-semibold rounded-3">
                                            Lihat Invoice & Status Pembayaran
                                        </a>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <button type="button" id="resumePayBtn"
                                            class="btn btn-agro-primary w-100 py-3 fw-semibold rounded-3">
                                            Lanjutkan Bayar Sekarang
                                        </button>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <a href="{{ route('home') }}"
                                        class="btn btn-outline-secondary w-100 py-3 fw-semibold rounded-3">
                                        Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
@if (($payment->payment_method ?? null) !== 'manual_transfer')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
        data-client-key="{{ config('midtrans.client_key') }}">
</script>
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
            onError: function () {
                alert('Pembayaran gagal diproses. Silakan coba lagi.');
            },
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
@endpush
