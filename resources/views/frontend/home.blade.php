@extends('frontend.main')

@section('content')

    <!-- ================= HERO ================= -->
    <section class="hero-section">
        <picture>
            <img src="{{ storage_asset_url('reviews/SawahBandung.jpg', asset('frontend/img/logo.png')) }}" class="hero-bg">
        </picture>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-4 animate-fade-in">
                <i class="bi bi-leaf text-accent fs-4"></i>
                <span
                    class="text-white-50 tracking-widest text-uppercase small">{{ get_setting('app_name', 'Agro Tourism Bandung') }}</span>
                <i class="bi bi-leaf text-accent fs-4"></i>
            </div>
            <h1 class="display-3 fw-bold text-white mb-4 animate-fade-in animate-delay-1">
                Jelajahi Keindahan <br>
                <span class="text-accent">Alam Bandung</span>
            </h1>
            <p class="lead text-white-50 mb-5 mx-auto animate-fade-in animate-delay-2" style="max-width: 600px;">
                Rasakan keindahan agrowisata yang memadukan panorama alam yang hijau, udara segar pegunungan,
                dan kesegaran hasil bumi langsung dari alamnya.
            </p>
            <a href="#destinasi" class="btn btn-agro-accent animate-fade-in animate-delay-3">Lihat Destinasi</a>
        </div>
    </section>

    <!-- ================= DESTINASI ================= -->
    <section id="destinasi" class="py-5">
        <div class="container py-5">

            <!-- TITLE -->
            <div class="text-center mb-5">
                <span class="text-accent fw-semibold small tracking-widest text-uppercase">Destinasi Populer</span>
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

                        return (object) [
                            'name' => $label,
                            'count' => $count,
                        ];
                    })
                    ->sortBy('name')
                    ->values();
            @endphp

            <!-- FORM (TETAP SAMA) -->
            <form method="GET">

                <!-- SEARCH -->
                <div class="search-container mb-4">
                    <div class="position-relative mx-auto" style="max-width: 500px;">
                        <i class="bi bi-search search-icon"></i>

                        <input type="text" id="searchInput" name="search" class="search-input"
                            placeholder="Cari destinasi, wilayah, atau aktivitas..." value="{{ request('search') }}">

                        <button type="submit" class="search-clear d-none">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- FILTER -->
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">

                    <button type="button" value="all" class="region-pill active">
                        <i class="bi bi-grid-3x3-gap"></i>
                        Semua
                        <span class="count-badge">{{ $pakets->count() }}</span>
                    </button>

                    @foreach ($groupedAreas as $area)
                        <button type="button" value="{{ $area->name }}" class="region-pill">
                            <i class="bi bi-geo-alt"></i> {{ $area->name }}
                            <span class="count-badge">{{ $area->count }}</span>
                        </button>
                    @endforeach

                </div>

            </form>

            <!-- GRID -->
            <div class="row g-4">

                @forelse ($pakets as $paket)
                    <div class="col-md-6 col-lg-4 paket-item"
                        data-search="{{ strtolower(implode(' ', array_filter([$paket->nama_paket, $normalizeAreaName($paket->vendor->area->name ?? ''), $paket->vendor->name ?? '', is_array($paket->aktivitas ?? null) ? implode(' ', $paket->aktivitas) : '']))) }}"
                        data-area="{{ strtolower($normalizeAreaName($paket->vendor->area->name ?? '')) }}">

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">

                            <!-- IMAGE -->
                            <div class="position-relative img-container">
                                <img src="{{ $paket->photos->first()?->photo_url ?? 'https://via.placeholder.com/400x250' }}"
                                    class="w-100 img-zoom" style="height:230px; object-fit:cover;">

                                <span
                                    class="badge bg-light text-dark position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2">
                                    ⭐ {{ number_format($paket->reviews->avg('rating') ?? 0, 1) }}
                                </span>
                            </div>

                            <!-- BODY -->
                            <div class="p-4">

                                <h5 class="fw-bold mb-2">
                                    {{ $paket->nama_paket }}
                                </h5>

                                <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                                    <i class="bi bi-geo-alt"></i>

                                    <span>
                                        {{ $paket->vendor->name ?? '-' }}
                                    </span>

                                    @if ($paket->vendor && $paket->vendor->area)
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            {{ $normalizeAreaName($paket->vendor->area->name) }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-muted small mb-3">
                                    {{ \Illuminate\Support\Str::limit($paket->deskripsi, 80) }}
                                </p>

                                <!-- ================= AKTIVITAS ================= -->
                                <div class="mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse($paket->aktivitas ?? [] as $item)
                                            <span
                                                style="
                                            background:#e5e7eb;
                                            color:#1f2937;
                                            padding:6px 14px;
                                            border-radius:999px;
                                            font-size:13px;
                                            font-weight:500;
                                        ">
                                                {{ $item }}
                                            </span>
                                        @empty
                                            <span class="text-muted small">Tidak ada aktivitas</span>
                                        @endforelse
                                    </div>
                                </div>
                                <!-- ============================================================= -->

                                <div class="text-muted small mb-3">
                                    <i class="bi bi-clock"></i>
                                    {{ $paket->jam_awal ? \Carbon\Carbon::parse($paket->jam_awal)->format('H:i') : '-' }}
                                    -
                                    {{ $paket->jam_akhir ? \Carbon\Carbon::parse($paket->jam_akhir)->format('H:i') : '-' }}
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted d-block">Mulai dari</small>
                                        <div class="d-flex align-items-end gap-2">
                                            <span class="fs-4 fw-bold text-primary-agro font-display">
                                                Rp{{ number_format($paket->harga_paket ?? 0, 0, ',', '.') }}
                                            </span>
                                            <small class="text-muted">/orang</small>
                                        </div>
                                    </div>
                                    <a href="{{ route('detail', $paket->id) }}" class="btn btn-agro-primary">
                                        Lihat Detail
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                @empty
                    <div class="col-12 text-center py-5">
                        <h5 class="fw-bold">Data tidak ditemukan</h5>
                        <p class="text-muted">Coba kata kunci lain</p>
                    </div>
                @endforelse

            </div>

            <!-- NO RESULT TAMBAHAN -->
            <div id="noResultMessage" class="text-center py-5" style="display:none;">
                <i class="bi bi-search fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold">Tidak ada destinasi yang tersedia</h5>
                <p class="text-muted">Silakan pilih wilayah lain atau ubah kata kunci pencarian</p>
            </div>

        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="py-5 bg-secondary bg-opacity-25">
        <div class="container py-5">
            <div class="text-center mx-auto" style="max-width: 700px;">
                <i class="bi bi-leaf text-primary-agro fs-1 mb-3 d-block"></i>
                <h2 class="font-display display-6 fw-bold mb-4">
                    {{ get_setting('about_title', 'Tentang AgroBandung') }}
                </h2>
                <p class="text-muted lead">
                    {{ get_setting('about_description', 'AgroBandung adalah platform pemesanan tiket wisata agro di kawasan Bandung dan sekitarnya.') }}
                </p>
            </div>
        </div>
    </section>

@endsection

<!-- ================= JS ================= -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const searchInput = document.getElementById("searchInput");
        const buttons = document.querySelectorAll(".region-pill");
        const items = Array.from(document.querySelectorAll(".paket-item")).map((item) => ({
            element: item,
            searchText: item.dataset.search || "",
            area: item.dataset.area || ""
        }));
        const noResult = document.getElementById("noResultMessage");

        let currentFilter = "all";
        let frameId = null;

        items.forEach(({
            element
        }) => {
            element.addEventListener("transitionend", function(event) {
                if (event.propertyName !== "opacity") {
                    return;
                }

                if (element.classList.contains("paket-hide")) {
                    element.classList.add("paket-gone");
                }
            });
        });

        function normalizeText(value) {
            return value.toLowerCase().trim().replace(/\s+/g, " ");
        }

        function showItem(element) {
            element.classList.remove("paket-gone");

            requestAnimationFrame(() => {
                element.classList.remove("paket-hide");
            });
        }

        function hideItem(element) {
            element.classList.add("paket-hide");
        }

        function applyFilter() {

            const keyword = normalizeText(searchInput?.value || "");
            let visible = 0;

            items.forEach(({
                element,
                searchText,
                area
            }) => {

                const matchSearch =
                    keyword === "" ||
                    searchText.includes(keyword);

                let matchFilter =
                    currentFilter === "all" ||
                    area.includes(currentFilter);

                if (keyword !== "") {
                    matchFilter = true;
                }

                if (matchSearch && matchFilter) {
                    showItem(element);
                    visible++;
                } else {
                    hideItem(element);
                }
            });

            if (noResult) {
                noResult.style.display = visible === 0 ? "block" : "none";
            }
        }

        function filterData() {
            if (frameId) {
                cancelAnimationFrame(frameId);
            }

            frameId = requestAnimationFrame(() => {
                applyFilter();
                frameId = null;
            });
        }

        searchInput.addEventListener("input", function() {
            currentFilter = "all";
            buttons.forEach(b => b.classList.remove("active"));
            if (buttons.length > 0) {
                buttons[0].classList.add("active");
            }

            filterData();

        });

        // FILTER BUTTON
        buttons.forEach(btn => {
            btn.addEventListener("click", function(e) {

                e.preventDefault();

                currentFilter = (this.value || "all").toLowerCase();

                buttons.forEach(b => b.classList.remove("active"));
                this.classList.add("active");

                filterData();
            });
        });

    });
</script>

<!-- ================= CSS ================== -->
<style>
    .img-zoom {
        transition: transform .4s ease;
    }

    .img-container {
        overflow: hidden;
    }

    .img-container:hover .img-zoom {
        transform: scale(1.1);
    }

    .paket-hide {
        opacity: 0;
        transform: scale(.95);
        pointer-events: none;
    }

    .paket-item {
        transition: opacity .18s ease, transform .18s ease;
        will-change: transform, opacity;
    }

    .paket-gone {
        display: none;
    }
</style>
