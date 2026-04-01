@extends('frontend.main')

@push('styles')
    <link href="{{ asset('frontend/css/booking.css') }}" rel="stylesheet">
@endpush

@section('header')
    @include('frontend.layouts.header')
    @stack('styles')
<body>
@endsection

@section('content')

    <header class="bg-white border-bottom position-sticky top-0" style="z-index: 1000;">
        <div class="container">
            <div class="d-flex align-items-center gap-3 py-3">
                <a href="{{ route('detail', $paket->id) }}" class="btn btn-light rounded-circle p-2" aria-label="Kembali">
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
                                @php
                                    $mainPhoto = $paket->photos->first()?->photo_url ?? 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=200&q=80';
                                @endphp
                                <img src="{{ $mainPhoto }}"
                                    alt="{{ $paket->nama_paket }}" class="rounded-3"
                                    style="width: 70px; height: 70px; object-fit: cover;">
                                <div>
                                    <h2 class="font-display fs-5 fw-bold mb-1">{{ $paket->nama_paket }}</h2>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-geo-alt text-primary-agro"></i> {{ $paket->vendor->area->name ?? 'Bandung' }}
                                    </p>
                                    <div class="d-flex align-items-center gap-3 small text-muted">
                                        <span><i class="bi bi-star-fill star-filled"></i> {{ number_format($paket->reviews->avg('rating') ?? 0, 1) }}</span>
                                        <span><i class="bi bi-clock"></i> {{ $paket->jam_operasional }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Selection -->
                            <div class="mb-4">
                                <h3 class="fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-primary-agro"></i> Tanggal Kunjungan
                                </h3>
                                <input type="hidden" id="paketTourId" value="{{ $paket->id }}">
                                <input type="hidden" id="visitDate" value="">
                                <input type="hidden" id="visitDateSisa" value="">

                                {{-- Calendar input display --}}
                                <div class="position-relative">
                                    <div class="form-control form-control-lg d-flex align-items-center justify-content-between"
                                         id="calendarToggle"
                                         style="cursor: pointer; background: #fff;"
                                         onclick="toggleCalendar()">
                                        <span id="calendarInputDisplay" class="text-muted">-- Pilih Tanggal --</span>
                                        <i class="bi bi-calendar3 text-primary-agro"></i>
                                    </div>
                                </div>

                                {{-- Custom Calendar --}}
                                <div id="customCalendar" class="custom-calendar-wrapper d-none mt-2">
                                    <div class="custom-calendar card shadow-sm border-0">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="calendarPrev()" id="calPrevBtn">
                                                    <i class="bi bi-chevron-left"></i>
                                                </button>
                                                <span class="fw-semibold" id="calendarMonthYear"></span>
                                                <button type="button" class="btn btn-sm btn-light rounded-circle" onclick="calendarNext()" id="calNextBtn">
                                                    <i class="bi bi-chevron-right"></i>
                                                </button>
                                            </div>
                                            <div class="calendar-grid">
                                                <div class="calendar-header">
                                                    <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                                                </div>
                                                <div class="calendar-body" id="calendarBody"></div>
                                            </div>
                                            <div class="mt-2 pt-2 border-top d-flex gap-3 small">
                                                <span class="d-flex align-items-center gap-1"><span class="cal-legend cal-legend-available"></span> Tersedia</span>
                                                <span class="d-flex align-items-center gap-1"><span class="cal-legend cal-legend-full"></span> Penuh</span>
                                                <span class="d-flex align-items-center gap-1"><span class="cal-legend cal-legend-disabled"></span> Tidak tersedia</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-info-circle"></i> Pilih tanggal yang tersedia untuk kunjungan Anda
                                </p>
                            </div>

                            {{-- Pass available dates data to JS --}}
                            <script>
                                window.AVAILABLE_DATES = {!! json_encode($availableDates->map(function($d) {
                                    return [
                                        'date' => $d->tanggal,
                                        'kuota' => $d->kuota,
                                        'sisa' => $d->sisa,
                                    ];
                                })->values()) !!};
                            </script>
                        <div id="afterDateSection" style="display:none;">
                            <!-- Pricing Rules -->
                            @if($paket->pricingRules->count() > 0)
                            <div class="mb-4" id="sectionDiskon">
                                <h3 class="fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-tag text-accent"></i> Penawaran Spesial (Diskon)
                                </h3>
                                <div class="row g-2">
                                    @foreach($paket->pricingRules as $rule)
                                    <div class="col-md-4 col-6">
                                        <div class="price-tier-card discount-card"
                                                data-min="{{ $rule->min_pax }}"
                                                data-max="{{ $rule->max_pax }}"
                                                data-type="{{ $rule->discount_type }}"
                                                data-value="{{ $rule->discount_value }}"
                                                onclick="selectDiscount(this)">
                                            <p class="text-muted small mb-0">{{ $rule->min_pax }}{{ $rule->max_pax ? '-' . $rule->max_pax : '+' }} orang</p>
                                            <p class="font-display fs-6 fw-bold text-primary-agro mb-0">
                                                @if($rule->discount_type === 'percent')
                                                    Potongan {{ $rule->discount_value }}%
                                                @elseif($rule->discount_type === 'nominal')
                                                    Potongan Rp{{ number_format($rule->discount_value, 0, ',', '.') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($paket->bundlings->where('is_active', true)->count() > 0)
                            <div class="mb-4" id="sectionBundling">
                                <h3 class="fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-box2-heart text-primary-agro"></i> Pilihan Paket Bundling
                                </h3>
                                <div class="row g-2">
                                    @foreach($paket->bundlings->where('is_active', true)->sortBy('people_count') as $bundling)
                                        <div class="col-md-4 col-6">
                                            <div class="price-tier-card bundling-card"
                                                    data-id="{{ $bundling->id }}"
                                                    data-people="{{ $bundling->people_count }}"
                                                    data-price="{{ $bundling->bundle_price }}"
                                                    data-label="{{ $bundling->label ?: ($paket->nama_paket . ' Bundling') }}"
                                                    data-description="{{ e($bundling->description ?: 'Paket bundling ini cocok untuk rombongan dengan kebutuhan kunjungan yang sudah dikemas praktis.') }}"
                                                    data-photos='@json($bundling->photos->pluck("photo_url")->filter()->values())'
                                                    onclick="selectBundling(this)">
                                                <button
                                                    type="button"
                                                    class="bundling-remove-btn"
                                                    onclick="clearBundlingSelection(event)"
                                                    aria-label="Hapus pilihan bundling">
                                                    &times;
                                                </button>
                                                <p class="text-muted small mb-2 bundling-card-label">
                                                    {{ $bundling->label ?: ($paket->nama_paket . ' Bundling') }}
                                                </p>
                                                <p class="text-muted small mb-2 bundling-card-people-text">
                                                    {{ number_format($bundling->people_count, 0, ',', '.') }} orang
                                                </p>
                                                <p class="font-display fs-6 fw-bold text-primary-agro mb-0 bundling-card-price-text">
                                                    Rp{{ number_format($bundling->bundle_price, 0, ',', '.') }}
                                                </p>
                                                <button type="button" class="bundling-detail-link" onclick="openBundlingDetail(event, this)">
                                                    <i class="bi bi-eye"></i> Lihat detail
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            <!-- Activities -->
<div class="mb-4" id="sectionAktivitas">
    <h3 class="fs-6 fw-semibold mb-2">Aktivitas yang tersedia:</h3>
    <div class="d-flex flex-wrap gap-2">
        @if(is_array($paket->aktivitas))
            @foreach($paket->aktivitas as $aktivitas)
                <span class="badge-activity">{{ $aktivitas }}</span>
            @endforeach
        @else
            <span class="badge-activity">-</span>
        @endif
    </div>
</div>

{{-- UMKM ADD ON --}}
@if($paket->umkmProducts && $paket->umkmProducts->count() > 0)
<hr class="my-4">

<div class="mb-2" id="sectionUmkm">
    <h3 class="fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-bag text-primary-agro"></i> Produk UMKM
    </h3>

    <div class="d-flex flex-column gap-2">
        @foreach($paket->umkmProducts as $product)
        @php
            $productPhoto = $product->photos->first()?->photo_url ?? asset('frontend/img/logo.png');
        @endphp
        <div class="d-flex align-items-center justify-content-between border rounded-3 p-3 umkm-item"
             data-id="{{ $product->id }}"
             data-price="{{ $product->price }}">

            <!-- kiri -->
            <div class="d-flex align-items-center gap-3"
                 style="cursor:pointer"
                 onclick="openProductModal(
                     @js($product->name),
                     @js($product->price),
                     @js($productPhoto),
                     @js($product->description ?? 'Produk UMKM lokal pilihan dari destinasi ini.')
                 )">
                <img src="{{ $productPhoto }}"
                     style="width:60px;height:60px;object-fit:cover;border-radius:10px;">

                <div>
                    <p class="fw-semibold small mb-0">{{ $product->name }}</p>
                    <p class="text-muted small mb-0">
                        Rp{{ number_format($product->price,0,',','.') }}
                    </p>
                </div>
            </div>

            <!-- kanan -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="decreaseUmkm({{ $product->id }})">-</button>

                <span id="qty-{{ $product->id }}">0</span>

                <button type="button" class="btn btn-sm btn-outline-secondary"
                        onclick="increaseUmkm({{ $product->id }})">+</button>
            </div>

        </div>
        @endforeach
    </div>

    <div class="bg-agro-light rounded-3 p-3 d-flex gap-2 mt-3">
        <i class="bi bi-info-circle text-primary-agro flex-shrink-0"></i>
        <p class="text-muted small mb-0">
            Produk UMKM diambil saat masuk ke area agrowisata.
        </p>
    </div>
</div>

<input type="hidden" id="umkmData" name="umkm_data">
@endif

</div> <!-- card-body -->
</div> <!-- card -->

</div> <!-- bookingStep1 -->
</div>
                                                            
                <!-- STEP 2: Data Peserta -->
                <div class="booking-step d-none" id="bookingStep2">
                    <div class="card card-agro">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <h3 class="fs-5 fw-semibold mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-people text-primary-agro"></i>
                                    Data Peserta (<span id="participantTotal">1</span>)
                                </h3>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-medium">Nama Penanggung Jawab</label>
                                <input type="text" id="customerName" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium d-flex align-items-center gap-1">
                                    <i class="bi bi-telephone"></i> No. Telepon
                                </label>
                                <input type="tel" id="customerPhone" class="form-control" placeholder="08xxxxxxxxxx" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-medium d-flex align-items-center gap-1">
                                    <i class="bi bi-envelope"></i> Email Pemesan
                                </label>
                                <input type="email" id="customerEmail" class="form-control" placeholder="email@example.com" required>
                            </div>
                            <div class="mb-2" id="participantInputWrapper">
                                <label class="form-label small fw-medium">Jumlah Peserta</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="decreaseParticipantCount()" aria-label="Kurangi peserta">-</button>
                                    <input type="number" id="participantCountInput" class="form-control text-center" min="1" value="1" oninput="validateParticipantInput()" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="increaseParticipantCount()" aria-label="Tambah peserta">+</button>
                                </div>
                                <small class="text-muted">Untuk banyak peserta, cukup isi jumlahnya saja.</small>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-medium">Nama Peserta Lain (Opsional)</label>
                                <textarea id="participantNotes" class="form-control" rows="3" placeholder="Contoh: Andi, Budi, Citra"></textarea>
                                <small class="text-muted">Field ini hanya catatan, tidak wajib diisi.</small>
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
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="payment-icon-circle">
                                                <i class="bi bi-wallet2 fs-4 text-primary-agro"></i>
                                            </div>
                                            <div>
                                                <p class="fw-bold mb-0">Pembayaran Otomatis (Midtrans)</p>
                                                <p class="text-muted small mb-0">Virtual Account, E-Wallet, Kartu Kredit, dll</p>
                                            </div>
                                        </div>
                                        <input type="radio" name="payment" value="midtrans" class="form-check-input" checked>
                                    </div>
                                </label>
                            </div>

                            <div class="bg-agro-light rounded-3 p-3 mt-4">
                                <div class="d-flex gap-2">
                                    <i class="bi bi-info-circle text-primary-agro flex-shrink-0"></i>
                                    <div>
                                        <p class="fw-semibold small mb-1">Informasi Pembayaran:</p>
                                        <ul class="text-muted small mb-0 ps-3">
                                            <li>Pembayaran diproses secara aman melalui Midtrans.</li>
                                            <li>Tiket akan langsung aktif setelah pembayaran berhasil.</li>
                                            <li>Invoice akan dikirimkan ke email Anda secara otomatis.</li>
                                        </ul>
                                    </div>
                                </div>
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
                            <a id="continuePaymentLink" href="#" class="btn btn-agro-primary w-100 d-none"
                                style="max-width: 400px;">
                                Lanjutkan Pembayaran
                            </a>
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
                            <a href="{{ route('home') }}" class="btn btn-agro-primary w-100" style="max-width: 400px;">
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
                            <h3 class="font-display fs-6 fw-semibold mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-receipt text-primary-agro"></i> Ringkasan Pesanan
                            </h3>

                            {{-- Destination info --}}
                            <div class="d-flex gap-3 mb-3 pb-3 border-bottom align-items-center">
                                @php
                                    $mainPhoto = $paket->photos->first()?->photo_url ?? 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=100&q=80';
                                @endphp
                                <img src="{{ $mainPhoto }}"
                                    alt="{{ $paket->nama_paket }}" class="flex-shrink-0"
                                    style="width: 56px; height: 56px; object-fit: cover; border-radius: 50%;" loading="lazy">
                                <div class="min-w-0">
                                    <p class="fw-semibold small mb-1" style="word-wrap: break-word; overflow-wrap: break-word;">{{ $paket->nama_paket }}</p>
                                    <p class="text-muted small mb-0" style="word-wrap: break-word; overflow-wrap: break-word;">
                                        <i class="bi bi-geo-alt-fill text-primary-agro"></i> {{ $paket->vendor->area->name ?? 'Bandung' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Date & Kuota --}}
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center small mb-2">
                                    <span class="text-muted d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar-event"></i> Tanggal
                                    </span>
                                    <span class="fw-semibold" id="summaryDate">-</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small d-none" id="summaryKuotaRow">
                                    <span class="text-muted d-flex align-items-center gap-1">
                                        <i class="bi bi-people"></i> Sisa Kuota
                                    </span>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-semibold" id="summaryKuota">-</span>
                                </div>
                            </div>

                            {{-- Price breakdown --}}
                            <div class="mb-3 pb-3 border-bottom" id="priceBreakdown">
                                <div class="d-flex justify-content-between align-items-start gap-3 small mb-2">
                                    <div>
                                        <p class="summary-group-label mb-1">Tiket</p>
                                        <span class="text-muted" id="summaryPriceLabel">
                                        Rp{{ number_format($paket->harga_paket, 0, ',', '.') }} × 1
                                        </span>
                                    </div>
                                    <span class="fw-medium text-end" id="summarySubtotal">
                                        Rp{{ number_format($paket->harga_paket, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div id="umkmSummaryList" class="mt-3"></div>
                                <div id="discountRow"></div>
                            </div>


                            {{-- Total --}}
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="fw-semibold">Total</span>
                                <span class="font-display fs-4 fw-bold text-primary-agro" id="totalPrice">Rp{{ number_format($paket->harga_paket, 0, ',', '.') }}</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-4 mb-5" id="bookingNav">
            <div class="row g-3">
                <div class="col-12 col-md-6" id="btnBackCol">
                    <button class="btn btn-outline-secondary w-100 py-3 fw-semibold" onclick="prevStep()">
                        Kembali
                    </button>
                </div>
                <div class="col-12 col-md-6" id="btnNextCol">
                    <button class="btn btn-agro-primary w-100 py-3 fw-semibold" id="btnNext" onclick="nextStep()">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- MODAL PRODUK (DESIGN CLEAN) -->
<div id="productModal" class="modal-overlay" onclick="closeProductModal(event)">
    <div class="modal-sheet">
        <button type="button" class="modal-close-btn" aria-label="Tutup" onclick="closeProductModalDirect()">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="modal-handle"></div>

        <div class="modal-img-wrap">
            <img id="modalImage" class="modal-img">
        </div>

        <div class="mt-3 text-start px-1">
            <h6 id="modalName" class="fw-semibold mb-1"></h6>
            <p id="modalPrice" class="text-primary-agro fw-bold mb-2" style="font-size:16px;"></p>
            <p id="modalDesc" class="text-muted small mb-0">
                Produk UMKM lokal pilihan dari destinasi ini.
            </p>
        </div>

    </div>
</div>

<div id="bundlingDetailModal" class="modal-overlay" onclick="closeBundlingDetail(event)">
    <div class="modal-sheet bundling-detail-sheet">
        <button type="button" class="modal-close-btn" aria-label="Tutup" onclick="closeBundlingDetailDirect()">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="modal-handle"></div>

        <div class="bundling-detail-content">
            <span class="bundling-detail-badge">Detail Bundling</span>
            <h5 id="bundlingDetailName" class="fw-semibold mb-3"></h5>

            <div id="bundlingDetailGallery" class="bundling-detail-gallery"></div>

            <div class="bundling-detail-meta">
                <div class="bundling-detail-meta-item">
                    <span class="text-muted small">Jumlah Peserta</span>
                    <strong id="bundlingDetailPeople"></strong>
                </div>
                <div class="bundling-detail-meta-item">
                    <span class="text-muted small">Harga Paket</span>
                    <strong id="bundlingDetailPrice" class="text-primary-agro"></strong>
                </div>
            </div>

            <div class="bundling-detail-description">
                <p class="text-muted small mb-1">Informasi Paket</p>
                <p id="bundlingDetailDescription" class="mb-0"></p>
            </div>
        </div>
    </div>
</div>
@endsection



<style>
#btnNext{
    width:100% !important;
    display:block;
}

@media (max-width:768px){

    #btnNextCol{
        flex:0 0 100% !important;
        max-width:100% !important;
        width:100% !important;
    }

    #btnNext{
        width:100% !important;
        display:block !important;
    }

}

#bookingNav .btn-agro-primary{
    border-radius:12px !important;
}

#bookingNav .btn-outline-secondary{
    border-radius:12px !important;
}

.discount-card {
    cursor: pointer;
    transition: 0.2s;
    position: relative;
}

.discount-card:hover {
    transform: scale(1.02);
}

.discount-card.active {
    border: 2px solid #198754;
    background: #f6fffa;
}

.discount-card.active::after {
    content: '×';
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #55786b;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1;
    box-shadow: 0 8px 18px rgba(18, 58, 45, 0.14);
    border: 1px solid rgba(85, 120, 107, 0.08);
}

.bundling-card {
    position: relative;
    min-height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: left;
    padding: 18px 18px 16px;
    border-radius: 18px;
    border-color: #d6e2db;
    box-shadow: 0 10px 24px rgba(17, 24, 39, 0.05);
}

.bundling-card.active::after {
    content: 'Ã—';
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #55786b;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1;
    box-shadow: 0 8px 18px rgba(18, 58, 45, 0.14);
    border: 1px solid rgba(85, 120, 107, 0.08);
}

.price-tier-card {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #fff;
}

/* hover effect */
.price-tier-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

/* active (kayak tombol folder lu) */
.price-tier-card.active {
    border: 2px solid #198754;
    background: #e9f7ef;
    box-shadow: 0 6px 18px rgba(25, 135, 84, 0.25);
}

/* teks ikut berubah */
.price-tier-card.active p:last-child {
    color: #198754;
}

.bundling-card-name {
    line-height: 1.35;
    min-height: 2.7em;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.bundling-card-chip {
    align-self: flex-start;
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.7rem;
    margin-bottom: 0.9rem;
    border-radius: 999px;
    background: #eef6f1;
    color: #2f6d4f;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.bundling-card-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.bundling-card-people {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.75rem;
    border-radius: 999px;
    background: #f5f7f6;
    color: #52616b;
    font-size: 0.82rem;
    font-weight: 600;
}

.bundling-card-price {
    color: #1f6b4f;
    font-family: var(--bs-font-sans-serif);
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.2;
}

.bundling-card {
    text-align: left;
    padding: 18px 18px 16px;
    border-radius: 18px;
    border-color: #d6e2db;
    box-shadow: 0 10px 24px rgba(17, 24, 39, 0.05);
}

.bundling-card.active::after {
    content: '\2713';
    top: 14px;
    right: 14px;
    width: 28px;
    height: 28px;
    background: #2f6d4f;
    color: #fff;
    font-size: 0.95rem;
    font-weight: 700;
    box-shadow: 0 10px 22px rgba(47, 109, 79, 0.28);
    border: none;
}

.bundling-card {
    justify-content: space-between;
    padding: 16px 18px 18px;
    border-radius: 16px;
    border-color: #d9e3dd;
    box-shadow: 0 8px 22px rgba(17, 24, 39, 0.04);
}

.bundling-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.85rem;
    padding-right: 1.75rem;
}

.bundling-card-body {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    min-height: 86px;
}

.bundling-card-footer {
    margin-top: 1rem;
    padding-top: 0.9rem;
    border-top: 1px solid #edf2ee;
}

.bundling-card-name {
    color: #22313f;
    font-size: 1.05rem;
    font-weight: 600;
    line-height: 1.45;
    min-height: 2.9em;
}

.bundling-card-chip {
    padding: 0.32rem 0.72rem;
    margin-bottom: 0;
    font-size: 0.74rem;
}

.bundling-card-meta {
    gap: 0.45rem;
}

.bundling-card-people {
    padding: 0.38rem 0.72rem;
}

.bundling-card-price {
    font-family: inherit;
    font-size: 1.15rem;
    font-weight: 700;
}

.bundling-card.active::after {
    content: '\00d7';
    top: 12px;
    right: 12px;
    width: 26px;
    height: 26px;
    background: #fff;
    color: #55786b;
    font-size: 1rem;
    font-weight: 600;
    box-shadow: 0 8px 18px rgba(18, 58, 45, 0.14);
    border: 1px solid rgba(85, 120, 107, 0.08);
}

.bundling-card.active .bundling-card-chip {
    background: rgba(47, 109, 79, 0.12);
    color: #21513b;
}

.bundling-card.active .bundling-card-people {
    background: rgba(47, 109, 79, 0.1);
    color: #21513b;
}

.bundling-card.active .bundling-card-price {
    color: #21513b;
}

.bundling-card-name {
    color: #1f2937;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.45;
    min-height: 2.9em;
}

#sectionBundling .row {
    row-gap: 12px;
}

.bundling-card {
    min-height: 124px;
    position: relative;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 14px 18px;
    border-radius: 18px;
    border: 1px solid #d7dee3;
    box-shadow: none;
    background: #fff;
    width: 100%;
}

.bundling-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.08);
}

.bundling-card-label,
.bundling-card-people-text {
    max-width: 100%;
    line-height: 1.45;
    overflow-wrap: break-word;
}

.bundling-card-label {
    font-size: 0.94rem;
    font-weight: 500;
    color: #5f6b76 !important;
}

.bundling-card-people-text {
    font-size: 0.92rem;
    color: #5f6b76 !important;
}

.bundling-card-price-text {
    font-size: 0.98rem !important;
    line-height: 1.3;
}

.bundling-detail-link {
    margin-top: 0.6rem;
    padding: 0;
    border: none;
    background: transparent;
    color: #2f6d4f;
    font-size: 0.84rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.bundling-detail-link:hover {
    color: #21513b;
    text-decoration: underline;
}

.bundling-card.active {
    border: 2px solid #198754;
    background: #e9f7ef;
    box-shadow: 0 6px 18px rgba(25, 135, 84, 0.25);
}

.bundling-remove-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 36px;
    height: 36px;
    border-radius: 999px;
    display: none;
    align-items: center;
    justify-content: center;
    background: #fff;
    color: #55786b;
    font-size: 1.1rem;
    font-weight: 500;
    line-height: 1;
    box-shadow: 0 8px 18px rgba(18, 58, 45, 0.14);
    border: 1px solid rgba(85, 120, 107, 0.08);
    z-index: 3;
    cursor: pointer;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}

.bundling-card.active .bundling-remove-btn {
    display: inline-flex;
}

.bundling-remove-btn:focus {
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.2), 0 8px 18px rgba(18, 58, 45, 0.14);
}

.bundling-card-chip,
.bundling-card-header,
.bundling-card-body,
.bundling-card-footer,
.bundling-card-meta,
.bundling-card-people,
.bundling-card-price {
    all: unset;
}

.bundling-detail-sheet {
    max-width: 520px;
}

.bundling-detail-content {
    padding: 0.5rem 0.25rem 0.25rem;
}

.bundling-detail-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.75rem;
    border-radius: 999px;
    background: #eef6f1;
    color: #2f6d4f;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.bundling-detail-meta {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.85rem;
    margin-bottom: 1rem;
}

.bundling-detail-gallery {
    position: relative;
    margin-bottom: 1rem;
}

.bundling-detail-slider {
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #e8efea;
    background: #f8faf8;
    aspect-ratio: 16 / 9;
}

.bundling-detail-slider img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.bundling-detail-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.92);
    color: #2f6d4f;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}

.bundling-detail-nav.prev {
    left: 12px;
}

.bundling-detail-nav.next {
    right: 12px;
}

.bundling-detail-counter {
    position: absolute;
    right: 12px;
    bottom: 12px;
    padding: 0.32rem 0.65rem;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.72);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 600;
    z-index: 2;
}

.bundling-detail-gallery-empty {
    padding: 1rem;
    border-radius: 14px;
    border: 1px dashed #d6e2db;
    background: #fbfcfb;
    color: #64748b;
    font-size: 0.9rem;
    text-align: center;
}

.bundling-detail-meta-item {
    padding: 0.9rem 1rem;
    border: 1px solid #e8efea;
    border-radius: 14px;
    background: #fbfcfb;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.bundling-detail-description {
    padding: 1rem;
    border-radius: 14px;
    background: #f8faf8;
    border: 1px solid #e8efea;
    color: #334155;
    line-height: 1.6;
}

@media (max-width: 576px) {
    .bundling-detail-nav {
        width: 34px;
        height: 34px;
    }

    .bundling-detail-meta {
        grid-template-columns: 1fr;
    }
}

.summary-addon-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    font-size: 0.875rem;
    margin-top: 10px;
}

.summary-addon-label {
    color: #6c757d;
}

.summary-addon-group {
    border-top: 1px dashed #dee2e6;
    padding-top: 12px;
    margin-top: 12px;
}

.summary-group-label {
    color: #495057;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.summary-discount-row {
    color: #dc3545;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(8, 26, 19, 0.56);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    display: flex;
    justify-content: center;
    align-items: center; 
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.28s ease, visibility 0.28s ease;
    padding: 24px;
}

.modal-sheet {
    width: 100%;
    max-width: 760px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fcf9 100%);
    border-radius: 24px;
    padding: 24px;
    border: 1px solid rgba(18, 94, 66, 0.14);
    box-shadow: 0 20px 48px rgba(0, 0, 0, 0.22);
    transform: translateY(20px) scale(0.96);
    opacity: 0;
    transition: transform 0.3s ease, opacity 0.3s ease;
    position: relative;
}

.modal-close-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.92);
    color: #355748;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    transition: all 0.2s ease;
    z-index: 2;
}

.modal-close-btn:hover {
    background: #ffffff;
    color: #163a2d;
}

.modal-handle {
    width: 48px;
    height: 5px;
    background: #d7e3dc;
    border-radius: 999px;
    margin: 0 auto 14px;
}

.modal-img-wrap {
    width: 100%;
    height: 300px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 10px 24px rgba(19, 68, 49, 0.18);
    background: #f1f5f2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    transform: scale(1);
    transition: none;
}
.umkm-item:hover {
    transform: scale(1.01);
    transition: 0.2s;
}
.modal-content-text {
    display: flex;
    justify-content: center;
}
#modalName {
    font-size: 1.9rem;
    line-height: 1.2;
    margin-bottom: 4px !important;
}
#modalPrice {
    font-size: 1.55rem !important;
    margin-bottom: 8px !important;
}
#modalDesc {
    line-height: 1.5;
    color: #5a6f65 !important;
    font-size: 1.02rem !important;
}

.modal-content-text .content-inner {
    text-align: center;
    max-width: 260px; 
}
.modal-content-text {
    text-align: center;
}
.modal-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}
.modal-overlay.active .modal-sheet {
    transform: translateY(0) scale(1);
    opacity: 1;
}

@media (max-width: 768px) {
    .modal-overlay {
        align-items: flex-end;
        padding: 12px;
    }

    .modal-sheet {
        max-width: none;
        width: 100%;
        border-radius: 20px 20px 14px 14px;
        padding: 16px;
        transform: translateY(26px);
    }

    .modal-close-btn {
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
    }

    .modal-handle {
        margin-bottom: 12px;
    }

    .modal-img-wrap {
        height: 220px;
        border-radius: 16px;
    }

    #modalName {
        font-size: 1.35rem;
    }

    #modalPrice {
        font-size: 1.15rem !important;
    }

    #modalDesc {
        font-size: 0.98rem !important;
    }
}

@media (max-width: 420px) {
    .modal-overlay {
        padding: 8px;
    }

    .modal-sheet {
        padding: 14px;
        border-radius: 18px 18px 12px 12px;
    }

    .modal-img-wrap {
        height: 184px;
        border-radius: 12px;
    }

    #modalName {
        font-size: 1.2rem;
    }

    #modalPrice {
        font-size: 1.05rem !important;
    }

    #modalDesc {
        font-size: 0.95rem !important;
    }
}
</style>


<script>
let basePrice = {{ $paket->harga_paket }};
let umkm = {};
let isBundlingSelected = false;
let minPax = 1;
let maxPax = null;
let isRendering = false;
let isUpdatingTotal = false;

function syncParticipantConstraintState() {
    const input = document.getElementById('participantCountInput');
    const participantTotal = document.getElementById('participantTotal');

    if (!input) {
        return;
    }

    const normalizedMin = Number.isFinite(minPax) && minPax > 0 ? minPax : 1;
    const normalizedMax = Number.isFinite(maxPax) && maxPax > 0 ? maxPax : null;
    let currentValue = parseInt(input.value, 10);

    if (!Number.isFinite(currentValue) || currentValue < normalizedMin) {
        currentValue = normalizedMin;
    }

    if (normalizedMax !== null && currentValue > normalizedMax) {
        currentValue = normalizedMax;
    }

    input.min = normalizedMin;
    if (normalizedMax !== null) {
        input.max = normalizedMax;
    } else {
        input.removeAttribute('max');
    }

    input.value = currentValue;

    if (participantTotal) {
        participantTotal.innerText = currentValue;
    }

    updateParticipantButtons();
}

// ================= UMKM =================
function increaseUmkm(id) {
    if (!umkm[id]) umkm[id] = 0;
    umkm[id]++;

    document.getElementById('qty-' + id).innerText = umkm[id];
    updateTotal();
}

function decreaseUmkm(id) {
    if (!umkm[id]) return;

    if (umkm[id] > 0) {
        umkm[id]--;
    }

    document.getElementById('qty-' + id).innerText = umkm[id];
    updateTotal();
}

function selectBundling(el) {
    if (el.classList.contains('active')) {
        clearSelectedBundling();
        return;
    }

    const bundlingPeople = parseInt(el.dataset.people, 10) || 1;
    const remainingQuota = parseInt(document.getElementById('visitDateSisa')?.value || '', 10);

    if (Number.isFinite(remainingQuota) && bundlingPeople > remainingQuota) {
        if (typeof window.showToast === 'function') {
            window.showToast('Sisa kuota tidak cukup untuk paket bundling ini.');
        } else {
            alert('Sisa kuota tidak cukup untuk paket bundling ini.');
        }
        return;
    }

    clearSelectedDiscount();
    document.querySelectorAll('.bundling-card').forEach(card => {
        card.classList.remove('active');
    });

    el.classList.add('active');
    isBundlingSelected = true;

    minPax = bundlingPeople;
    maxPax = bundlingPeople;

    const input = document.getElementById('participantCountInput');
    if (input) {
        input.value = bundlingPeople;
    }

    syncParticipantConstraintState();
    if (typeof window.syncParticipantCountFromInput === 'function') {
        window.syncParticipantCountFromInput();
    }
    updateTotal();
}

function clearBundlingSelection(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    clearSelectedBundling();
}

function clearSelectedBundling() {
    document.querySelectorAll('.bundling-card').forEach(card => {
        card.classList.remove('active');
    });

    isBundlingSelected = false;
    minPax = 1;
    maxPax = null;

    syncParticipantConstraintState();
    if (typeof window.syncParticipantCountFromInput === 'function') {
        window.syncParticipantCountFromInput();
    }
    updateTotal();
}

// ================= DISKON =================
function selectDiscount(el) {

    
    if (el.classList.contains('active')) {
        clearSelectedDiscount();
        return;
    }

    clearSelectedBundling();

    // 
    document.querySelectorAll('.discount-card').forEach(card => {
        card.classList.remove('active');
    });

    // 
    el.classList.add('active');

    // 
    minPax = parseInt(el.dataset.min);
    maxPax = el.dataset.max ? parseInt(el.dataset.max) : null;

    const input = document.getElementById('participantCountInput');

    // paksa ke minimum pricing rule yang dipilih
    input.value = minPax;

    syncParticipantConstraintState();
    if (typeof window.syncParticipantCountFromInput === 'function') {
        window.syncParticipantCountFromInput();
    }
    updateTotal();
}

function clearSelectedDiscount() {
    document.querySelectorAll('.discount-card').forEach(card => {
        card.classList.remove('active');
    });

    if (isBundlingSelected) {
        return;
    }

    minPax = 1;
    maxPax = null;

    syncParticipantConstraintState();
    if (typeof window.syncParticipantCountFromInput === 'function') {
        window.syncParticipantCountFromInput();
    }
    updateTotal();
}

// ================= TOTAL =================
function updateTotal() {

    const input = document.getElementById('participantCountInput');
    let pax = parseInt(input.value, 10) || 1;

    if (pax < minPax) pax = minPax;
    if (maxPax !== null && pax > maxPax) pax = maxPax;
    input.value = pax;

    const participantTotal = document.getElementById('participantTotal');
    if (participantTotal) {
        participantTotal.innerText = pax;
    }

    let totalUmkm = 0;
    let umkmItemsHtml = '';

    document.querySelectorAll('.umkm-item').forEach(el => {
        const id = el.dataset.id;
        const price = parseFloat(el.dataset.price);
        const qty = umkm[id] || 0;

        if (qty > 0) {
            totalUmkm += price * qty;
            const productName = el.querySelector('.fw-semibold.small')?.innerText?.trim() || 'Add-on UMKM';
            umkmItemsHtml += `
                <div class="summary-addon-item">
                    <span class="text-muted">${productName} x ${qty}</span>
                    <span class="fw-medium text-end">Rp${(price * qty).toLocaleString('id-ID')}</span>
                </div>
            `;
        }
    });

    if (typeof window.syncParticipantCountFromInput === 'function') {
        window.syncParticipantCountFromInput();
    }

    const pricingSummary = typeof window.getPriceCalculation === 'function'
        ? window.getPriceCalculation()
        : {
            totalBase: basePrice * pax,
            discount: 0,
            totalPrice: basePrice * pax
        };

    const umkmSummaryList = document.getElementById('umkmSummaryList');
    if (umkmSummaryList) {
        umkmSummaryList.innerHTML = umkmItemsHtml ? `
            <div class="summary-addon-group">
                <p class="summary-group-label mb-1">Add-on</p>
                ${umkmItemsHtml}
            </div>
        ` : '';
    }

    const totalPrice = document.getElementById('totalPrice');
    if (totalPrice) {
        totalPrice.innerText = 'Rp' + (pricingSummary.totalPrice + totalUmkm).toLocaleString('id-ID');
    }
}

    
   

function lockParticipantInput(lock) {

    let input = document.getElementById('participantCountInput');
    let btnPlus = input.nextElementSibling;
    let btnMinus = input.previousElementSibling;

    if (lock) {
        input.setAttribute('readonly', true);
        input.classList.add('bg-light');

        btnPlus.disabled = true;
        btnMinus.disabled = true;

        btnPlus.classList.add('opacity-50');
        btnMinus.classList.add('opacity-50');

    } else {
        input.removeAttribute('readonly');
        input.classList.remove('bg-light');

        btnPlus.disabled = false;
        btnMinus.disabled = false;

        btnPlus.classList.remove('opacity-50');
        btnMinus.classList.remove('opacity-50');
    }
}
function updateParticipantButtons() {

    let input = document.getElementById('participantCountInput');
    let btnPlus = input.nextElementSibling;
    let btnMinus = input.previousElementSibling;

    let value = parseInt(input.value, 10) || minPax;

    // MIN LIMIT
    if (value <= minPax) {
        btnMinus.disabled = true;
        btnMinus.classList.add('opacity-50');
    } else {
        btnMinus.disabled = false;
        btnMinus.classList.remove('opacity-50');
    }

    // MAX LIMIT
    if (maxPax && value >= maxPax) {
        btnPlus.disabled = true;
        btnPlus.classList.add('opacity-50');
    } else {
        btnPlus.disabled = false;
        btnPlus.classList.remove('opacity-50');
    }
}
function increaseParticipantCount() {
    let input = document.getElementById('participantCountInput');
    let value = parseInt(input.value, 10) || minPax;

    //VALIDASI MAX FIX
    if (maxPax !== null && value >= maxPax) {
        input.value = maxPax; 
        updateParticipantButtons();
        return;
    }

    input.value = value + 1;

    updateParticipantButtons();
    updateTotal();
}

function decreaseParticipantCount() {
    let input = document.getElementById('participantCountInput');
    let value = parseInt(input.value, 10) || minPax;

    if (value <= minPax) {
        input.value = minPax;
        updateParticipantButtons();
        return;
    }

    input.value = value - 1;

    updateParticipantButtons();
    updateTotal();
}
function validateParticipantInput() {
    let input = document.getElementById('participantCountInput');
    let value = parseInt(input.value, 10) || minPax;

    if (value < minPax) value = minPax;
    if (maxPax !== null && value > maxPax) value = maxPax;

    input.value = value;

    updateParticipantButtons();
    updateTotal();
}

// ================= AUTO PATCH SYSTEM =================

document.addEventListener('DOMContentLoaded', function () {

    updateTotal();
    syncParticipantConstraintState();

    const input = document.getElementById('participantCountInput');

    if (!input) return;

    input.addEventListener('input', function () {
        enforceLimits();
        updateTotal();
    });

});

// ================= HARD LIMIT GLOBAL =================
function enforceLimits() {

    let input = document.getElementById('participantCountInput');
    if (!input) return;

    let value = parseInt(input.value) || minPax;

    if (value < minPax) value = minPax;
    if (maxPax !== null && value > maxPax) value = maxPax;

    input.value = value;

    updateParticipantButtons();

}

// ================= PATCH NEXT STEP =================
(function () {

    const originalNextStep = window.nextStep;

    if (!originalNextStep) return;

    window.nextStep = function () {
        syncParticipantConstraintState();

        let input = document.getElementById('participantCountInput');
        let value = parseInt(input.value, 10) || minPax;

        
        if (value < minPax || (maxPax !== null && value > maxPax)) {
            alert('Jumlah peserta tidak sesuai paket!');
            enforceLimits();
            return;
        }

        originalNextStep(); // lanjut normal
    };

})();

// ================= PATCH SELECT DISKON =================
(function () {

    const originalSelectDiscount = window.selectDiscount;

    if (!originalSelectDiscount) return;

    window.selectDiscount = function (el) {

        originalSelectDiscount(el);

        
        updateParticipantButtons();
    };

})();



// ================= SHOW AFTER DATE =================
function showAfterDateSection() {
    document.getElementById('sectionDiskon')?.classList.remove('d-none');
    document.getElementById('sectionBundling')?.classList.remove('d-none');
    document.getElementById('sectionAktivitas')?.classList.remove('d-none');
    document.getElementById('sectionUmkm')?.classList.remove('d-none');
}

// ================= FIX FINAL (WORK 100%) =================
document.addEventListener('DOMContentLoaded', function () {

    const calendarBody = document.getElementById('calendarBody');

    if (!calendarBody) return;

    calendarBody.addEventListener('click', function(e) {

        const day = e.target.closest('div');

        if (!day) return;

        
        if (day.innerText.trim() === '') return;

     
        document.getElementById('afterDateSection').style.display = 'block';

    });

});

function openProductModal(name, price, image, desc) {

    document.getElementById('modalName').innerText = name;
    document.getElementById('modalPrice').innerText =
        'Rp' + parseInt(price).toLocaleString('id-ID');
    document.getElementById('modalDesc').innerText = desc;
    document.getElementById('modalImage').src = image;

    document.getElementById('productModal').classList.add('active');
}

function closeProductModal(e) {
    if (e.target.classList.contains('modal-overlay')) {
        document.getElementById('productModal').classList.remove('active');
    }
}

function closeProductModalDirect() {
    document.getElementById('productModal').classList.remove('active');
}

function openBundlingDetail(event, trigger) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    const card = trigger.closest('.bundling-card');
    if (!card) return;

    const label = card.dataset.label || 'Paket Bundling';
    const people = parseInt(card.dataset.people || '0', 10);
    const price = parseInt(card.dataset.price || '0', 10);
    const description = card.dataset.description || 'Paket bundling ini cocok untuk rombongan dengan kebutuhan kunjungan yang sudah dikemas praktis.';
    let photos = [];

    try {
        photos = JSON.parse(card.dataset.photos || '[]');
    } catch (error) {
        photos = [];
    }

    document.getElementById('bundlingDetailName').innerText = label;
    document.getElementById('bundlingDetailPeople').innerText = `${people.toLocaleString('id-ID')} orang`;
    document.getElementById('bundlingDetailPrice').innerText = 'Rp' + price.toLocaleString('id-ID');
    document.getElementById('bundlingDetailDescription').innerText = description;

    window.currentBundlingPhotos = photos;
    window.currentBundlingPhotoIndex = 0;

    const gallery = document.getElementById('bundlingDetailGallery');
    if (gallery) {
        if (photos.length > 0) {
            renderBundlingPhotoSlider(label);
        } else {
            gallery.innerHTML = `
                <div class="bundling-detail-gallery-empty">
                    Foto bundling belum tersedia.
                </div>
            `;
        }
    }

    document.getElementById('bundlingDetailModal').classList.add('active');
}

function closeBundlingDetail(event) {
    if (event.target.classList.contains('modal-overlay')) {
        document.getElementById('bundlingDetailModal').classList.remove('active');
    }
}

function closeBundlingDetailDirect() {
    document.getElementById('bundlingDetailModal').classList.remove('active');
}

function renderBundlingPhotoSlider(label) {
    const gallery = document.getElementById('bundlingDetailGallery');
    const photos = window.currentBundlingPhotos || [];
    const index = window.currentBundlingPhotoIndex || 0;

    if (!gallery || photos.length === 0) {
        return;
    }

    gallery.innerHTML = `
        <div class="bundling-detail-slider">
            ${photos.length > 1 ? '<button type="button" class="bundling-detail-nav prev" onclick="changeBundlingPhoto(-1)" aria-label="Foto sebelumnya"><i class="bi bi-chevron-left"></i></button>' : ''}
            <img src="${photos[index]}" alt="${label} photo ${index + 1}">
            ${photos.length > 1 ? '<button type="button" class="bundling-detail-nav next" onclick="changeBundlingPhoto(1)" aria-label="Foto berikutnya"><i class="bi bi-chevron-right"></i></button>' : ''}
            ${photos.length > 1 ? `<span class="bundling-detail-counter">${index + 1}/${photos.length}</span>` : ''}
        </div>
    `;
}

function changeBundlingPhoto(direction) {
    const photos = window.currentBundlingPhotos || [];
    if (photos.length <= 1) {
        return;
    }

    const total = photos.length;
    const current = window.currentBundlingPhotoIndex || 0;
    window.currentBundlingPhotoIndex = (current + direction + total) % total;

    const label = document.getElementById('bundlingDetailName')?.innerText || 'Bundling';
    renderBundlingPhotoSlider(label);
}

</script>
