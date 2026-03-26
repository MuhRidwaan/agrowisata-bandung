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

                        <!-- Tombol -->
                        <div class="mt-4 mb-5">
                            <div class="row g-3">

                                <div class="col-12">
                                    <button type="button" id="resumePayBtn"
                                        class="btn btn-agro-primary w-100 py-3 fw-semibold rounded-3">
                                        Lanjutkan Bayar Sekarang
                                    </button>
                                </div>

                                <div class="col-12">
                                    <a href="{{ route('home') }}"
                                        class="btn btn-outline-secondary w-100 py-3 fw-semibold rounded-3">
                                        Kembali ke Beranda
                                    </a>
                                </div>

                            </div>
                        </div>
                        <!-- End Tombol -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
        data-client-key="{{ config('midtrans.client_key') }}">
</script>
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
@endpush
