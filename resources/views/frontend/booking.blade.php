@extends('frontend.main')

@section('content')
    <header class="bg-white border-bottom position-sticky top-0" style="z-index: 1000;">
        <div class="container">
            <div class="d-flex align-items-center gap-3 py-3">
                <a href="detail-strawberry.html" class="btn btn-light rounded-circle p-2" aria-label="Kembali">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square text-primary-agro"></i>
                    <span class="font-display fs-5 fw-bold">Pesan Tiket</span>
                </div>
            </div>
        </div>
    </header>

    <div class="container py-4">
        <!-- 4-Step Stepper -->
        <div class="booking-stepper mb-4">
            <div class="booking-stepper-item active" data-step="1">
                <div class="booking-stepper-circle">
                    <span class="step-number">1</span>
                    <i class="bi bi-check-lg step-check d-none"></i>
                </div>
                <span class="booking-stepper-label">Pilih Tiket</span>
            </div>
            <div class="booking-stepper-line"></div>
            <div class="booking-stepper-item" data-step="2">
                <div class="booking-stepper-circle">
                    <span class="step-number">2</span>
                    <i class="bi bi-check-lg step-check d-none"></i>
                </div>
                <span class="booking-stepper-label">Data Peserta</span>
            </div>
            <div class="booking-stepper-line"></div>
            <div class="booking-stepper-item" data-step="3">
                <div class="booking-stepper-circle">
                    <span class="step-number">3</span>
                    <i class="bi bi-check-lg step-check d-none"></i>
                </div>
                <span class="booking-stepper-label">Pembayaran</span>
            </div>
            <div class="booking-stepper-line"></div>
            <div class="booking-stepper-item" data-step="4">
                <div class="booking-stepper-circle">
                    <span class="step-number">4</span>
                    <i class="bi bi-check-lg step-check d-none"></i>
                </div>
                <span class="booking-stepper-label">Konfirmasi</span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8" id="mainContentCol">

                <!-- STEP 1: Pilih Tiket -->
                <div class="booking-step" id="bookingStep1">
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <!-- Destination Info -->
                            <div class="d-flex gap-3 mb-4 pb-4 border-bottom">
                                <img src="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=200&q=80"
                                    alt="Kebun Strawberry Ciwidey" class="rounded-3"
                                    style="width: 70px; height: 70px; object-fit: cover;">
                                <div>
                                    <h2 class="font-display fs-5 fw-bold mb-1">Kebun Strawberry Ciwidey</h2>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-geo-alt text-primary-agro"></i> Ciwidey, Bandung Selatan
                                    </p>
                                    <div class="d-flex align-items-center gap-3 small text-muted">
                                        <span><i class="bi bi-star-fill star-filled"></i> 4.7</span>
                                        <span><i class="bi bi-clock"></i> 08:00 - 17:00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Selection -->
                            <div class="mb-4">
                                <h3 class="fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-primary-agro"></i> Tanggal Kunjungan
                                </h3>
                                <input type="date" class="form-control form-control-lg" id="visitDate" required>
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-info-circle"></i> Pemesanan minimal 24 jam sebelum kunjungan
                                </p>
                            </div>

                            <!-- Price Tiers -->
                            <div class="mb-4">
                                <h3 class="fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-tag text-accent"></i> Harga berdasarkan jumlah peserta
                                </h3>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="price-tier-card active">
                                            <p class="text-muted small mb-0">1-4 orang</p>
                                            <p class="font-display fs-5 fw-bold text-primary-agro mb-0">Rp50.000</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="price-tier-card">
                                            <p class="text-muted small mb-0">5-9 orang</p>
                                            <p class="font-display fs-5 fw-bold text-primary-agro mb-0">Rp45.000</p>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="price-tier-card">
                                            <p class="text-muted small mb-0">10+ orang</p>
                                            <p class="font-display fs-5 fw-bold text-primary-agro mb-0">Rp40.000</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activities -->
                            <div>
                                <h3 class="fs-6 fw-semibold mb-2">Aktivitas yang tersedia:</h3>
                                <div class="d-flex flex-wrap gap-2">

                                    <span class="badge-activity">Petik Strawberry</span>
                                    <span class="badge-activity">Foto Instagramable</span>
                                    <span class="badge-activity">Café &amp; Resto</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Data Peserta -->
                <div class="booking-step d-none" id="bookingStep2">
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <h3 class="fs-5 fw-semibold mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-people text-primary-agro"></i>
                                    Data Peserta (<span id="participantTotal">1</span>)
                                </h3>
                                <button type="button"
                                    class="btn btn-sm btn-link text-primary-agro fw-semibold text-decoration-none"
                                    onclick="addParticipant()">
                                    + Tambah Peserta
                                </button>
                            </div>

                            <div id="participantsList">
                                <div class="participant-card mb-3" data-participant="1">
                                    <p class="fw-semibold mb-3">Peserta 1</p>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium">Nama Lengkap</label>
                                        <input type="text" class="form-control" placeholder="Masukkan nama lengkap"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-medium d-flex align-items-center gap-1">
                                            <i class="bi bi-telephone"></i> No. Telepon
                                        </label>
                                        <input type="tel" class="form-control" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                    <div>
                                        <label class="form-label small fw-medium d-flex align-items-center gap-1">
                                            <i class="bi bi-envelope"></i> Email Pemesan
                                        </label>
                                        <input type="email" class="form-control" placeholder="email@example.com"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-agro-light rounded-3 p-3 d-flex gap-2 mt-3">
                                <i class="bi bi-shield-check text-primary-agro flex-shrink-0"></i>
                                <p class="text-muted small mb-0">
                                    Data pribadi dilindungi dan hanya digunakan untuk keperluan pemesanan tiket.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Pembayaran -->
                <div class="booking-step d-none" id="bookingStep3">
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h3 class="fs-5 fw-semibold mb-4 d-flex align-items-center gap-2">
                                <i class="bi bi-credit-card text-primary-agro"></i> Metode Pembayaran
                            </h3>
                            <div class="d-flex flex-column gap-3">
                                <label class="payment-method-card">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="fw-medium mb-0">Transfer Bank</p>
                                            <p class="text-muted small mb-0">BCA, Mandiri, BRI, BNI</p>
                                        </div>
                                        <input type="radio" name="payment" value="transfer" class="form-check-input">
                                    </div>
                                </label>
                                <label class="payment-method-card">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="fw-medium mb-0">E-Wallet</p>
                                            <p class="text-muted small mb-0">GoPay, OVO, Dana, ShopeePay</p>
                                        </div>
                                        <input type="radio" name="payment" value="ewallet" class="form-check-input">
                                    </div>
                                </label>
                                <label class="payment-method-card">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <p class="fw-medium mb-0">QRIS</p>
                                            <p class="text-muted small mb-0">Scan QR dari semua bank & e-wallet</p>
                                        </div>
                                        <input type="radio" name="payment" value="qris" class="form-check-input">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Konfirmasi -->
                <div class="booking-step d-none" id="bookingStep4">
                    <!-- Waiting Payment -->
                    <div class="card card-agro" id="paymentWaiting">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <div class="spinner-border text-accent" style="width: 3rem; height: 3rem;"
                                    role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <h3 class="font-display fs-4 fw-bold mb-2">Menunggu Pembayaran</h3>
                            <p class="text-muted mb-4">
                                Silakan selesaikan pembayaran Anda melalui <strong id="paymentMethodName">-</strong>
                            </p>
                            <div class="bg-agro-light rounded-3 p-3 mb-3 d-inline-block">
                                <p class="text-muted small mb-1">Total yang harus dibayar</p>
                                <p class="font-display fs-4 fw-bold text-primary-agro mb-0" id="waitingTotal">Rp0</p>
                            </div>
                            <div class="border rounded-3 p-3 mb-4 mx-auto" style="max-width: 300px;">
                                <p class="text-muted small mb-1">Kode Pemesanan</p>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <span class="font-display fs-5 fw-bold" id="bookingCode">-</span>
                                    <button class="btn btn-sm btn-light" onclick="copyBookingCode()" title="Salin kode">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-agro-primary w-100" onclick="confirmPayment()"
                                style="max-width: 400px;">
                                Saya Sudah Bayar
                            </button>
                        </div>
                    </div>

                    <!-- Payment Success -->
                    <div class="card card-agro d-none" id="paymentSuccess">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <div class="bg-primary-agro rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px;">
                                    <i class="bi bi-check-lg fs-1 text-white"></i>
                                </div>
                            </div>
                            <h3 class="font-display fs-4 fw-bold mb-2">Pembayaran Berhasil!</h3>
                            <p class="text-muted mb-3">Kode pemesanan Anda:</p>
                            <div class="d-inline-block bg-agro-light rounded-3 px-4 py-2 mb-4">
                                <span class="font-display fs-4 fw-bold text-primary-agro" id="successBookingCode">-</span>
                            </div>
                            <div class="border rounded-3 p-3 mb-4 mx-auto text-start" style="max-width: 400px;">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="bi bi-geo-alt text-primary-agro mt-1"></i>
                                    <div>
                                        <p class="text-muted small mb-0">Destinasi</p>
                                        <p class="fw-medium small mb-0" id="successDestination">-</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="bi bi-calendar text-primary-agro mt-1"></i>
                                    <div>
                                        <p class="text-muted small mb-0">Tanggal</p>
                                        <p class="fw-medium small mb-0" id="successDate">-</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-people text-primary-agro mt-1"></i>
                                    <div>
                                        <p class="text-muted small mb-0" id="successParticipantLabel">Peserta (1 orang)
                                        </p>
                                        <p class="fw-medium small mb-0" id="successParticipantName">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-4 mx-auto"
                                style="max-width: 400px;">
                                <span class="text-muted">Total Bayar</span>
                                <span class="font-display fs-4 fw-bold text-primary-agro" id="successTotal">Rp0</span>
                            </div>
                            <a href="index.html" class="btn btn-agro-primary w-100" style="max-width: 400px;">
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar: Order Summary -->
            <div class="col-lg-4" id="orderSummaryCol">
                <div class="position-sticky" style="top: 80px;">
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <h3 class="font-display fs-5 fw-semibold mb-3">Ringkasan Pesanan</h3>
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                                <img src="https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=100&q=80"
                                    alt="Kebun Strawberry Ciwidey" class="rounded-3"
                                    style="width: 50px; height: 50px; object-fit: cover;" loading="lazy">
                                <div>
                                    <p class="fw-medium small mb-0">Kebun Strawberry Ciwidey</p>
                                    <p class="text-muted small mb-0"><i class="bi bi-geo-alt"></i> Ciwidey, Bandung
                                        Selatan</p>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Tanggal</span>
                                    <span class="fw-medium" id="summaryDate">-</span>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted" id="summaryPriceLabel">Rp50.000 &times; 1</span>
                                    <span id="summarySubtotal">Rp50.000</span>
                                </div>
                                <div class="d-flex justify-content-between small">
                                    <span class="text-muted">Biaya Layanan</span>
                                    <span id="summaryServiceFee">Rp2.500</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-medium">Total</span>
                                <span class="font-display fs-4 fw-bold text-primary-agro" id="totalPrice">Rp52.500</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-4 mb-5" id="bookingNav">
            <div class="row g-3">
                <div class="col-6 d-none" id="btnBackCol">
                    <button class="btn btn-outline-secondary w-100 py-3 fw-semibold" onclick="prevStep()">
                        Kembali
                    </button>
                </div>
                <div class="col-12" id="btnNextCol">
                    <button class="btn btn-agro-primary w-100 py-3 fw-semibold" id="btnNext" onclick="nextStep()">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
