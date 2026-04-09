@extends('frontend.main')

@section('content')

    <!-- ================= HERO ================= -->
    <section class="hero-section" aria-label="Hero">
        <picture>
            <img src="{{ storage_asset_url('reviews/SawahBandung.jpg', asset('frontend/img/logo.png')) }}"
                 class="hero-bg" alt="Pemandangan Agrowisata Bandung" loading="eager">
        </picture>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-3 animate-fade-in">
                <i class="bi bi-leaf text-accent" aria-hidden="true"></i>
                <span class="text-white-50 tracking-widest text-uppercase small fw-semibold">
                    {{ get_setting('app_name', 'Agro Tourism Bandung') }}
                </span>
                <i class="bi bi-leaf text-accent" aria-hidden="true"></i>
            </div>
            <h1 class="font-display fw-bold text-white mb-4 animate-fade-in animate-delay-1 hero-title">
                Jelajahi Keindahan <br>
                <span class="text-accent">Alam Bandung</span>
            </h1>
            <p class="lead text-white-50 mb-5 mx-auto animate-fade-in animate-delay-2 hero-desc">
                Rasakan keindahan agrowisata yang memadukan panorama alam yang hijau, udara segar pegunungan,
                dan kesegaran hasil bumi langsung dari alamnya.
            </p>
            <a href="#destinasi" class="btn btn-agro-accent animate-fade-in animate-delay-3">
                <i class="bi bi-compass me-2" aria-hidden="true"></i>Lihat Destinasi
            </a>
        </div>
    </section>

    <!-- ================= DESTINASI ================= -->
    <section id="destinasi" class="py-5" aria-label="Daftar Destinasi">
        <div class="container py-4 py-lg-5">

            <!-- TITLE -->
            <div class="text-center mb-5">
                <span class="section-title-badge">Destinasi Populer</span>
                <h2 class="font-display display-5 fw-bold mt-2 mb-3">Wisata Agro Terbaik</h2>
                <p class="text-muted mx-auto" style="max-width: 500px;">
                    Pilih destinasi favorit Anda dan pesan tiket secara online dengan mudah dan cepat.
                </p>
            </div>

            @php
                $areaAliases = [
                    'pengalengan' => 'Pangalengan',
                    'pangalengan' => 'Pangalengan',
                ];

                $normalizeAreaName = function (?string $name) use ($areaAliases) {
                    $cleanName = trim((string) $name);
                    $key = strtolower($cleanName);
                    return $areaAliases[$key] ?? $cleanName;
                };

                $groupedAreas = collect($areas)
                    ->groupBy(fn($area) => $normalizeAreaName($area->name))
                    ->map(function ($items, $label) use ($pakets, $normalizeAreaName) {
                        $count = $pakets->filter(function ($paket) use ($label, $normalizeAreaName) {
                            return $normalizeAreaName(optional(optional($paket->vendor)->area)->name) === $label;
                        })->count();
                        return (object) ['name' => $label, 'count' => $count];
                    })
                    ->sortBy('name')
                    ->values();
            @endphp

            <!-- SEARCH -->
            <div class="search-container mb-4">
                <div class="position-relative mx-auto" style="max-width: 520px;">
                    <i class="bi bi-search search-icon" aria-hidden="true"></i>
                    <input type="text" id="searchInput" class="search-input"
                        placeholder="Cari destinasi, wilayah, atau aktivitas..."
                        value="{{ request('search') }}"
                        aria-label="Cari destinasi">
                    <button type="button" id="searchClearBtn" class="search-clear d-none" aria-label="Hapus pencarian">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- FILTER PILLS — scrollable on mobile -->
            <div class="filter-pills-wrapper mb-4" role="tablist" aria-label="Filter wilayah">
                <div class="filter-pills-inner justify-content-lg-center">
                    <button type="button" value="all" class="region-pill active"
                            role="tab" aria-selected="true">
                        <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                        Semua
                        <span class="count-badge">{{ $pakets->count() }}</span>
                    </button>
                    @foreach ($groupedAreas as $area)
                        <button type="button" value="{{ $area->name }}" class="region-pill"
                                role="tab" aria-selected="false">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            {{ $area->name }}
                            <span class="count-badge">{{ $area->count }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- GRID -->
            <div class="row g-4" id="paketGrid">
                @forelse ($pakets as $paket)
                    @php
                        $aktivitasList = is_array($paket->aktivitas) ? $paket->aktivitas : [];
                        $maxBadge = 3;
                        $extraCount = max(0, count($aktivitasList) - $maxBadge);
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4 paket-item"
                         data-search="{{ strtolower(implode(' ', array_filter([$paket->nama_paket, $normalizeAreaName($paket->vendor->area->name ?? ''), $paket->vendor->name ?? '', implode(' ', $aktivitasList)]))) }}"
                         data-area="{{ strtolower($normalizeAreaName($paket->vendor->area->name ?? '')) }}">

                        <article class="card card-agro destination-card h-100 border-0">

                            <!-- IMAGE -->
                            <div class="card-img-wrapper position-relative">
                                <img src="{{ $paket->photos->first()?->photo_url ?? 'https://via.placeholder.com/400x250' }}"
                                     class="card-img-top"
                                     alt="{{ $paket->nama_paket }}"
                                     loading="lazy"
                                     width="400" height="220">

                                <!-- Rating badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-white text-dark shadow-sm rounded-pill px-2 py-1 d-flex align-items-center gap-1"
                                          style="font-size:0.8rem;">
                                        <i class="bi bi-star-fill text-warning" aria-hidden="true"></i>
                                        {{ number_format($paket->reviews->avg('rating') ?? 0, 1) }}
                                    </span>
                                </div>

                                <!-- Area badge -->
                                @if ($paket->vendor && $paket->vendor->area)
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="background:rgba(45,106,79,0.85);color:#fff;font-size:0.75rem;backdrop-filter:blur(4px);">
                                        <i class="bi bi-geo-alt-fill me-1" aria-hidden="true"></i>
                                        {{ $normalizeAreaName($paket->vendor->area->name) }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            <!-- BODY -->
                            <div class="card-body d-flex flex-column p-4">

                                <h3 class="font-display fs-5 fw-bold mb-1 line-clamp-2">
                                    {{ $paket->nama_paket }}
                                </h3>

                                <p class="text-muted small mb-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-shop" aria-hidden="true"></i>
                                    {{ $paket->vendor->name ?? '-' }}
                                </p>

                                <p class="text-muted small mb-3 line-clamp-2">
                                    {{ \Illuminate\Support\Str::limit($paket->deskripsi, 90) }}
                                </p>

                                <!-- Aktivitas (max 3 + overflow) -->
                                @if(count($aktivitasList) > 0)
                                <div class="activity-badges-wrap mb-3">
                                    @foreach(array_slice($aktivitasList, 0, $maxBadge) as $item)
                                        <span class="badge-activity">{{ $item }}</span>
                                    @endforeach
                                    @if($extraCount > 0)
                                        <span class="badge-activity" style="background:rgba(45,106,79,0.1);color:var(--agro-primary);">
                                            +{{ $extraCount }} lagi
                                        </span>
                                    @endif
                                </div>
                                @endif

                                <!-- Jam operasional -->
                                <p class="text-muted small mb-3 d-flex align-items-center gap-1">
                                    <i class="bi bi-clock" aria-hidden="true"></i>
                                    {{ $paket->jam_awal ? \Carbon\Carbon::parse($paket->jam_awal)->format('H:i') : '-' }}
                                    –
                                    {{ $paket->jam_akhir ? \Carbon\Carbon::parse($paket->jam_akhir)->format('H:i') : '-' }}
                                </p>

                                <!-- Harga + CTA -->
                                <div class="mt-auto d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                    <div>
                                        <small class="text-muted d-block" style="font-size:0.72rem;">Mulai dari</small>
                                        <div class="d-flex align-items-baseline gap-1">
                                            <span class="fs-4 fw-bold text-primary-agro font-display">
                                                Rp{{ number_format($paket->harga_paket ?? 0, 0, ',', '.') }}
                                            </span>
                                            <small class="text-muted">/orang</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('detail', $paket->id) }}"
                                       class="btn btn-agro-primary flex-shrink-0"
                                       style="padding: 0.6rem 1.25rem; font-size: 0.875rem;">
                                        Lihat Detail
                                    </a>
                                </div>

                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-map fs-1 text-muted mb-3 d-block" aria-hidden="true"></i>
                        <h3 class="fs-5 fw-bold">Belum ada paket tersedia</h3>
                        <p class="text-muted">Coba lagi nanti atau hubungi kami.</p>
                    </div>
                @endforelse
            </div>

            <!-- NO RESULT -->
            <div id="noResultMessage" class="text-center py-5" style="display:none;" aria-live="polite">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block" aria-hidden="true"></i>
                <h3 class="fs-5 fw-bold">Tidak ada destinasi yang ditemukan</h3>
                <p class="text-muted">Silakan pilih wilayah lain atau ubah kata kunci pencarian</p>
            </div>

        </div>
    </section>

    <!-- ================= TENTANG ================= -->
    <section id="tentang" class="section-tentang-split overflow-hidden">
        <div class="row g-0 align-items-stretch">

            {{-- Kiri: warna hijau + konten --}}
            <div class="col-lg-6 tentang-left d-flex align-items-center">
                <div class="tentang-left-inner">
                    <span class="tentang-badge">Tentang Kami</span>
                    <h2 class="font-display fw-bold mb-4 lh-sm tentang-title">
                        {{ get_setting('about_title', 'Tentang Agrotourism Bandung') }}
                    </h2>
                    <p class="tentang-desc mb-5">
                        {{ get_setting('about_description', 'AgroBandung adalah platform pemesanan tiket wisata agro di kawasan Bandung dan sekitarnya. Kami menghubungkan wisatawan dengan destinasi agrowisata terbaik.') }}
                    </p>

                    {{-- Features --}}
                    <div class="tentang-features mt-4">
                        <div class="tentang-feature">
                            <div class="tentang-feature-icon"><i class="bi bi-calendar2-check-fill"></i></div>
                            <div>
                                <div class="tentang-feature-title">Booking Mudah</div>
                                <div class="tentang-feature-desc">Pesan kapan saja, tanpa antri.</div>
                            </div>
                        </div>
                        <div class="tentang-feature">
                            <div class="tentang-feature-icon"><i class="bi bi-headset"></i></div>
                            <div>
                                <div class="tentang-feature-title">Dukungan Langsung</div>
                                <div class="tentang-feature-desc">Siap bantu via WhatsApp.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kanan: gambar --}}
            <div class="col-lg-6 tentang-right">
                <div class="tentang-img-wrap">
                    <img src="{{ storage_asset_url(get_setting('about_image'), asset('frontend/img/logo.png')) }}"
                         alt="Agrowisata Bandung"
                         class="tentang-img"
                         loading="lazy">
                    <div class="tentang-img-overlay"></div>
                </div>
            </div>

        </div>
    </section>

    <!-- ================= KONTAK ================= -->
    <section id="kontak" class="py-5 section-kontak">
        <div class="container py-4 py-lg-5">

            <div class="text-center mb-5">
                <span class="section-title-badge">Hubungi Kami</span>
                <h2 class="font-display display-6 fw-bold mt-2 mb-3">Ada Pertanyaan?</h2>
                <p class="text-muted mx-auto" style="max-width:480px;">
                    Kami siap membantu Anda merencanakan kunjungan agrowisata yang sempurna.
                </p>
            </div>

            <div class="row g-4 justify-content-center">

                <!-- Info kontak -->
                <div class="col-12 col-lg-5">
                    <div class="kontak-info-card h-100">
                        <h3 class="font-display fs-4 fw-bold mb-4">Informasi Kontak</h3>

                        @php
                            $phone    = get_setting('contact_phone', '+62 856 2455 4616');
                            $email    = get_setting('contact_email', 'agrotourisminbandung@gmail.com');
                            $address  = get_setting('contact_address', 'Bandung, Jawa Barat');
                            $waNumber = preg_replace('/[^0-9]/', '', $phone);
                        @endphp

                        <div class="kontak-item">
                            <div class="kontak-icon-wrap">
                                <i class="bi bi-telephone-fill"></i>
                            </div>
                            <div>
                                <p class="kontak-label">Telepon / WhatsApp</p>
                                <a href="tel:{{ $phone }}" class="kontak-value">{{ $phone }}</a>
                            </div>
                        </div>

                        <div class="kontak-item">
                            <div class="kontak-icon-wrap">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <div>
                                <p class="kontak-label">Email</p>
                                <a href="mailto:{{ $email }}" class="kontak-value">{{ $email }}</a>
                            </div>
                        </div>

                        <div class="kontak-item">
                            <div class="kontak-icon-wrap">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div>
                                <p class="kontak-label">Lokasi</p>
                                <span class="kontak-value">{{ $address }}</span>
                            </div>
                        </div>

                        <div class="kontak-item">
                            <div class="kontak-icon-wrap">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <div>
                                <p class="kontak-label">Jam Operasional</p>
                                <span class="kontak-value d-block">{{ get_setting('weekday_hours', 'Senin - Jumat: 08:00 - 18:00') }}</span>
                                <span class="kontak-value d-block">{{ get_setting('weekend_hours', 'Sabtu - Minggu: 07:00 - 19:00') }}</span>
                            </div>
                        </div>

                        @if($waNumber)
                        <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo, saya ingin bertanya tentang paket wisata agro di Bandung.') }}"
                           target="_blank" rel="noopener"
                           class="btn-wa-kontak mt-4">
                            <i class="bi bi-whatsapp"></i>
                            Chat via WhatsApp
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Form kontak -->
                <div class="col-12 col-lg-7">
                    <div class="kontak-form-card h-100">
                        <h3 class="font-display fs-4 fw-bold mb-4">Kirim Pesan</h3>
                        <form id="kontakForm" novalidate>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-medium small" for="kontakNama">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="kontakNama" class="form-control kontak-input"
                                           placeholder="Nama Anda" required>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label fw-medium small" for="kontakEmail">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="kontakEmail" class="form-control kontak-input"
                                           placeholder="email@contoh.com" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small" for="kontakTelepon">No. Telepon</label>
                                    <input type="tel" id="kontakTelepon" class="form-control kontak-input"
                                           placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-medium small" for="kontakPesan">Pesan <span class="text-danger">*</span></label>
                                    <textarea id="kontakPesan" class="form-control kontak-input" rows="4"
                                              placeholder="Tulis pertanyaan atau pesan Anda di sini..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-kirim-pesan" id="btnKirimPesan">
                                        <i class="bi bi-send-fill me-2"></i>Kirim via WhatsApp
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p class="text-muted small mt-3 mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Pesan akan dikirim langsung ke WhatsApp kami untuk respons lebih cepat.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    var searchInput = document.getElementById("searchInput");
    var searchClearBtn = document.getElementById("searchClearBtn");
    var buttons = document.querySelectorAll(".region-pill");
    var items = Array.from(document.querySelectorAll(".paket-item")).map(function (item) {
        return {
            element: item,
            searchText: item.dataset.search || "",
            area: item.dataset.area || ""
        };
    });
    var noResult = document.getElementById("noResultMessage");
    var currentFilter = "all";
    var frameId = null;
    var debounceTimer = null;

    // Transition: hide → gone
    items.forEach(function (item) {
        item.element.addEventListener("transitionend", function (e) {
            if (e.propertyName !== "opacity") return;
            if (item.element.classList.contains("paket-hide")) {
                item.element.classList.add("paket-gone");
            }
        });
    });

    function normalizeText(v) {
        return v.toLowerCase().trim().replace(/\s+/g, " ");
    }

    function showItem(el) {
        el.classList.remove("paket-gone");
        requestAnimationFrame(function () { el.classList.remove("paket-hide"); });
    }

    function hideItem(el) { el.classList.add("paket-hide"); }

    function applyFilter() {
        var keyword = normalizeText(searchInput ? searchInput.value : "");
        var visible = 0;

        items.forEach(function (item) {
            var matchSearch = keyword === "" || item.searchText.includes(keyword);
            var matchFilter = currentFilter === "all" || item.area.includes(currentFilter.toLowerCase());
            if (keyword !== "") matchFilter = true;

            if (matchSearch && matchFilter) { showItem(item.element); visible++; }
            else { hideItem(item.element); }
        });

        if (noResult) noResult.style.display = visible === 0 ? "block" : "none";

        // Toggle clear button
        if (searchClearBtn) {
            searchClearBtn.classList.toggle("d-none", !keyword);
        }
    }

    function scheduleFilter() {
        if (frameId) cancelAnimationFrame(frameId);
        frameId = requestAnimationFrame(function () { applyFilter(); frameId = null; });
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            currentFilter = "all";
            buttons.forEach(function (b) {
                b.classList.remove("active");
                b.setAttribute("aria-selected", "false");
            });
            if (buttons.length > 0) {
                buttons[0].classList.add("active");
                buttons[0].setAttribute("aria-selected", "true");
            }
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(scheduleFilter, 180);
        });
    }

    if (searchClearBtn) {
        searchClearBtn.addEventListener("click", function () {
            if (searchInput) searchInput.value = "";
            searchClearBtn.classList.add("d-none");
            applyFilter();
            if (searchInput) searchInput.focus();
        });
    }

    buttons.forEach(function (btn) {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            currentFilter = (this.value || "all");
            buttons.forEach(function (b) {
                b.classList.remove("active");
                b.setAttribute("aria-selected", "false");
            });
            this.classList.add("active");
            this.setAttribute("aria-selected", "true");
            scheduleFilter();
        });
    });

    // Run initial filter if search param exists
    if (searchInput && searchInput.value) applyFilter();
});
</script>

<script>
// Form kontak — kirim via WhatsApp
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('kontakForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var nama    = document.getElementById('kontakNama').value.trim();
        var email   = document.getElementById('kontakEmail').value.trim();
        var telepon = document.getElementById('kontakTelepon').value.trim();
        var pesan   = document.getElementById('kontakPesan').value.trim();

        if (!nama || !email || !pesan) {
            // Highlight kosong
            [['kontakNama', nama], ['kontakEmail', email], ['kontakPesan', pesan]].forEach(function (pair) {
                var el = document.getElementById(pair[0]);
                if (el) el.classList.toggle('is-invalid', !pair[1]);
            });
            return;
        }

        var waNumber = '{{ preg_replace('/[^0-9]/', '', get_setting('contact_phone', '')) }}';
        if (!waNumber) {
            alert('Nomor WhatsApp belum dikonfigurasi.');
            return;
        }

        var msg = 'Halo, saya ingin menghubungi ' + '{{ get_setting('app_name', 'AgroBandung') }}' + '.\n\n'
            + '*Nama:* ' + nama + '\n'
            + '*Email:* ' + email + '\n'
            + (telepon ? '*Telepon:* ' + telepon + '\n' : '')
            + '\n*Pesan:*\n' + pesan;

        window.open('https://wa.me/' + waNumber + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');

        // Reset form
        form.reset();
        document.querySelectorAll('.kontak-input').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    });

    // Remove invalid on input
    form.querySelectorAll('.kontak-input').forEach(function (el) {
        el.addEventListener('input', function () { this.classList.remove('is-invalid'); });
    });
});
</script>
@endpush

<style>
/* ===== HERO ===== */
.hero-title { font-size: clamp(2rem, 6vw, 3.5rem); line-height: 1.15; }
.hero-desc { max-width: 580px; font-size: clamp(0.95rem, 2.5vw, 1.15rem); }

/* ===== CARD FILTER ===== */
.paket-hide { opacity: 0; transform: scale(.96); pointer-events: none; }
.paket-item { transition: opacity .2s ease, transform .2s ease; will-change: transform, opacity; }
.paket-gone { display: none; }
.destination-card { transition: box-shadow 0.3s ease, transform 0.3s ease; }
.destination-card:hover { box-shadow: var(--shadow-card-hover) !important; transform: translateY(-4px); }

/* ===== SECTION TENTANG — Split Layout ===== */
.section-tentang-split { background: var(--agro-bg); }

.tentang-left {
    background: var(--agro-primary);
    padding: 0;
}
.tentang-left-inner {
    padding: 4rem 3rem 4rem 4rem;
    max-width: 580px;
}

.tentang-badge {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    color: rgba(255,255,255,0.9);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 0.35rem 1rem;
    border-radius: 50rem;
    margin-bottom: 1.25rem;
}

.tentang-title {
    color: #fff;
    font-size: clamp(1.75rem, 3.5vw, 2.5rem);
    line-height: 1.2;
}

.tentang-desc {
    color: rgba(255,255,255,0.75);
    font-size: 0.95rem;
    line-height: 1.75;
}

/* Stats */
.tentang-stats {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}
.tentang-stat { text-align: center; }
.tentang-stat-num {
    font-size: 2rem;
    font-weight: 700;
    color: var(--agro-accent);
    line-height: 1;
}
.tentang-stat-label {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.6);
    margin-top: 4px;
}
.tentang-stat-divider {
    width: 1px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    flex-shrink: 0;
}

/* Features */
.tentang-features {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.tentang-feature {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.tentang-feature-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 10px;
    background: rgba(255,255,255,0.12);
    color: var(--agro-accent);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.tentang-feature-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 2px;
}
.tentang-feature-desc {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.6);
}

/* Right image — diagonal cut */
.tentang-right {
    position: relative;
    min-height: 500px;
}
.tentang-img-wrap {
    position: absolute;
    inset: 0;
    overflow: hidden;
}
.tentang-img {
    width: 100%; height: 100%;
    object-fit: cover;
    object-position: center;
}
.tentang-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(45,106,79,0.5) 0%, rgba(45,106,79,0.1) 35%, transparent 65%);
}

/* Diagonal divider — pseudo element di atas gambar, warna hijau miring */
.tentang-right::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 80px;
    height: 100%;
    background: var(--agro-primary);
    clip-path: polygon(0 0, 100% 0, 40% 100%, 0 100%);
    z-index: 2;
}

/* Kiri */
.tentang-left {
    background: var(--agro-primary);
    padding: 0;
    position: relative;
    z-index: 1;
}

/* Mobile */
@media (max-width: 991.98px) {
    .tentang-left { clip-path: none; }
    .tentang-left-inner { padding: 3rem 1.5rem; max-width: 100%; }
    .tentang-right { min-height: 260px; }
    .tentang-right::before { display: none; }
}

.about-stat-card {
    background: #fff;
    border: 1px solid rgba(45,106,79,0.12);
    border-radius: 14px;
    padding: 1.25rem 1rem;
    text-align: center;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
}
.about-stat-card:hover { box-shadow: var(--shadow-card-hover); transform: translateY(-2px); }
.about-stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: rgba(45,106,79,0.1);
    color: var(--agro-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    margin: 0 auto 0.5rem;
}
.about-stat-num { font-size: 1.75rem; font-weight: 700; color: var(--agro-primary); line-height: 1; }
.about-stat-label { font-size: 0.8rem; color: var(--agro-text-muted); margin-top: 4px; }

.about-features { display: flex; flex-direction: column; gap: 1.25rem; }
.about-feature-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: #fff;
    border: 1px solid rgba(45,106,79,0.1);
    border-radius: 14px;
    padding: 1.25rem;
    transition: box-shadow 0.25s ease;
}
.about-feature-item:hover { box-shadow: var(--shadow-card); }
.about-feature-icon {
    width: 44px; height: 44px; flex-shrink: 0;
    border-radius: 12px;
    background: rgba(45,106,79,0.1);
    color: var(--agro-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}
.about-feature-title { font-size: 0.95rem; font-weight: 700; color: var(--agro-text); margin-bottom: 4px; }
.about-feature-desc { font-size: 0.85rem; color: var(--agro-text-muted); margin: 0; line-height: 1.5; }

/* ===== SECTION KONTAK ===== */
.section-kontak { background: var(--agro-bg); }

.kontak-info-card {
    background: var(--agro-primary);
    border-radius: 20px;
    padding: 2rem;
    color: #fff;
}
.kontak-info-card h3 { color: #fff; }

.kontak-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.875rem 0;
    border-bottom: 1px solid rgba(255,255,255,0.12);
}
.kontak-item:last-of-type { border-bottom: none; }

.kontak-icon-wrap {
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 10px;
    background: rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    color: var(--agro-accent);
}
.kontak-label {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.6);
    margin-bottom: 2px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.kontak-value {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.92);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}
a.kontak-value:hover { color: var(--agro-accent); }

.btn-wa-kontak {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #25d366;
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    border: none;
    transition: background 0.2s ease, transform 0.15s ease;
    width: 100%;
    justify-content: center;
    min-height: 48px;
}
.btn-wa-kontak:hover { background: #20bd5a; color: #fff; transform: translateY(-1px); }

.kontak-form-card {
    background: #fff;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-card);
}
.kontak-form-card h3 { color: var(--agro-text); }

.kontak-input {
    border: 1.5px solid var(--agro-border);
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    background: #fafafa;
}
.kontak-input:focus {
    border-color: var(--agro-primary);
    box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
    background: #fff;
    outline: none;
}
.kontak-input::placeholder { color: #b0b8c1; }

.btn-kirim-pesan {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 50px;
    background: var(--agro-primary);
    color: #fff;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
}
.btn-kirim-pesan:hover {
    background: var(--agro-primary-light);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(45,106,79,0.3);
}
.btn-kirim-pesan:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

@media (max-width: 575.98px) {
    .kontak-info-card, .kontak-form-card { padding: 1.5rem; border-radius: 16px; }
}
</style>
