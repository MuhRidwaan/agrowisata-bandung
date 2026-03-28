@extends('frontend.main')

@section('content')

<!-- ================= HERO ================= -->
<section class="hero-section">
    <picture>
       <img src="{{ storage_asset_url('reviews/SawahBandung.jpg', asset('frontend/img/logo.png')) }}"
             class="hero-bg">
    </picture>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="d-flex align-items-center justify-content-center gap-2 mb-4 animate-fade-in">
            <i class="bi bi-leaf text-accent fs-4"></i>
            <span class="text-white-50 tracking-widest text-uppercase small">{{ get_setting('app_name', 'Agro Tourism Bandung') }}</span>
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

        <!-- FORM (TETAP SAMA) -->
        <form method="GET" id="destinationSearchForm">

            <!-- SEARCH -->
            <div class="search-container mb-4">
                <div class="position-relative mx-auto" style="max-width: 500px;">
                    <i class="bi bi-search search-icon"></i>

                    <input 
                        type="text" 
                        id="searchInput"
                        name="search"
                        class="search-input"
                        placeholder="Cari destinasi, wilayah, atau aktivitas..."
                        value="{{ request('search') }}"
                    >

                    <button type="button" class="search-clear d-none">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <!-- FILTER -->
            <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">

                <button type="button" value="all"
                    class="region-pill active">
                    <i class="bi bi-grid-3x3-gap"></i> 
                    Semua
                    <span class="count-badge">{{ $pakets->count() }}</span>
                </button>

                @foreach($areas as $area)
                <button type="button" value="{{ $area->name }}"
                    class="region-pill">
                    <i class="bi bi-geo-alt"></i> {{ $area->name }}
                    <span class="count-badge">
                        {{ $pakets->filter(fn($p) => $p->vendor->area_id == $area->id)->count() }}
                    </span>
                </button>
                @endforeach

            </div>

        </form>

        <!-- GRID -->
        <div class="row g-4" id="paketGrid">

            @forelse ($pakets as $paket)
            <div 
                class="col-md-6 col-lg-4 paket-item"
                data-name="{{ strtolower($paket->nama_paket) }}"
                data-area="{{ strtolower($paket->vendor->area->name ?? '') }}"
                data-vendor="{{ strtolower($paket->vendor->name ?? '') }}"
                data-activities="{{ strtolower(collect($paket->aktivitas ?? [])->implode(' ')) }}"
            >

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">

                    <!-- IMAGE -->
                    <div class="position-relative img-container">
                        <img 
                            src="{{ $paket->photos->first()?->photo_url ?? 'https://via.placeholder.com/400x250' }}"
                            class="w-100 img-zoom"
                            style="height:230px; object-fit:cover;"
                        >

                        <span class="badge bg-light text-dark position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2">
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

                            @if($paket->vendor && $paket->vendor->area)
                            <span class="badge bg-success-subtle text-success rounded-pill">
                                {{ $paket->vendor->area->name }}
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
                            <a href="{{ route('detail', $paket->id) }}"
                                class="btn btn-agro-primary">
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
                    {{ get_setting('about_title','Tentang AgroBandung') }}
                </h2>
                <p class="text-muted lead">
                    {{ get_setting('about_description','AgroBandung adalah platform pemesanan tiket wisata agro di kawasan Bandung dan sekitarnya.') }}
                </p>
            </div>
        </div>
    </section>

@endsection

<!-- ================= JS ================= -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchForm = document.getElementById("destinationSearchForm");
    const searchInput = document.getElementById("searchInput");
    const searchClear = document.querySelector(".search-clear");
    const buttons = Array.from(document.querySelectorAll(".region-pill"));
    const items = Array.from(document.querySelectorAll(".paket-item"));
    const noResult = document.getElementById("noResultMessage");

    if (!searchInput) {
        return;
    }

    const SEARCH_DELAY = 80;
    const HIDE_DELAY = 180;
    let currentFilter = "all";
    let searchTimer = null;

    function normalize(value) {
        return (value || "").toString().toLowerCase().trim();
    }

    function updateClearButton() {
        if (!searchClear) {
            return;
        }

        searchClear.classList.toggle("d-none", searchInput.value.trim() === "");
    }

    function setActiveFilterButton(value) {
        buttons.forEach((button) => {
            button.classList.toggle("active", normalize(button.value) === value);
        });
    }

    function showItem(item, animate) {
        if (item.hideTimer) {
            clearTimeout(item.hideTimer);
            item.hideTimer = null;
        }

        const wasHidden = item.style.display === "none";
        item.style.display = "";
        item.classList.remove("paket-hidden");

        if (wasHidden || animate) {
            item.classList.remove("paket-enter");
            item.offsetWidth;
            item.classList.add("paket-enter");
        }
    }

    function hideItem(item) {
        if (item.hideTimer) {
            clearTimeout(item.hideTimer);
        }

        item.classList.remove("paket-enter");
        item.classList.add("paket-hidden");
        item.hideTimer = setTimeout(() => {
            item.style.display = "none";
            item.hideTimer = null;
        }, HIDE_DELAY);
    }

    function matchesKeyword(item, keyword) {
        if (!keyword) {
            return true;
        }

        const haystacks = [
            item.dataset.name,
            item.dataset.area,
            item.dataset.vendor,
            item.dataset.activities,
        ];

        return haystacks.some((value) => normalize(value).includes(keyword));
    }

    function matchesFilter(item, filterValue, keyword) {
        if (keyword) {
            return true;
        }

        return filterValue === "all" || normalize(item.dataset.area).includes(filterValue);
    }

    function applyFilters() {
        const keyword = normalize(searchInput.value);
        const shouldAnimateMatches = keyword !== "";
        let visibleCount = 0;

        items.forEach((item) => {
            const shouldShow = matchesKeyword(item, keyword) && matchesFilter(item, currentFilter, keyword);

            if (shouldShow) {
                showItem(item, shouldAnimateMatches);
                visibleCount += 1;
            } else {
                hideItem(item);
            }
        });

        if (noResult) {
            noResult.style.display = items.length > 0 && visibleCount === 0 ? "block" : "none";
        }
    }

    if (searchForm) {
        searchForm.addEventListener("submit", function (event) {
            event.preventDefault();
        });
    }

    searchInput.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
        }
    });

    searchInput.addEventListener("input", function () {
        updateClearButton();

        if (searchTimer) {
            clearTimeout(searchTimer);
        }

        searchTimer = setTimeout(() => {
            currentFilter = "all";
            setActiveFilterButton("all");
            applyFilters();
        }, SEARCH_DELAY);
    });

    if (searchClear) {
        searchClear.addEventListener("click", function () {
            searchInput.value = "";
            currentFilter = "all";
            setActiveFilterButton("all");
            updateClearButton();
            applyFilters();
            searchInput.focus();
        });
    }

    buttons.forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            currentFilter = normalize(button.value) || "all";
            setActiveFilterButton(currentFilter);
            applyFilters();
        });
    });

    updateClearButton();
    setActiveFilterButton("all");
    applyFilters();
});


</script>

<!-- ================= CSS ================== -->
<style>
.img-zoom{
    transition: transform .4s ease;
}

.img-container{
    overflow:hidden;
}

.img-container:hover .img-zoom{
    transform: scale(1.1);
}
.paket-hidden{
    opacity:0;
    transform: translateY(6px);
    pointer-events:none;
}
.paket-item{
    transition: opacity .18s ease, transform .18s ease;
    will-change: transform, opacity;
    opacity: 1;
    transform: translateY(0);
}
.paket-enter{
    animation: paketFadeIn .22s ease-out;
}
@keyframes paketFadeIn{
    from{
        opacity: 0;
        transform: translateY(8px);
    }
    to{
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
