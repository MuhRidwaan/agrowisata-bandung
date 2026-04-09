<body>
    <a href="#main-content" class="visually-hidden-focusable skip-link">Langsung ke konten utama</a>

    <nav class="navbar navbar-expand-lg navbar-agro fixed-top" id="mainNavbar" role="navigation" aria-label="Navigasi utama">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <div class="brand-logo-wrap">
                    <img src="{{ setting_asset_url('app_logo') }}"
                         alt="{{ get_setting('app_name', 'AgroBandung') }}"
                         class="logo-navbar">
                </div>
                <span class="brand-name font-display fw-bold">{{ get_setting('app_name', 'AgroBandung') }}</span>
            </a>

            <!-- Toggler -->
            <button class="navbar-toggler border-0" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu">
                <div class="toggler-icon">
                    <span></span><span></span><span></span>
                </div>
            </button>

            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link nav-link-agro" href="{{ route('home') }}#destinasi">
                            <i class="bi bi-compass-fill nav-icon" aria-hidden="true"></i>
                            <span>Destinasi</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-agro" href="{{ route('home') }}#tentang">
                            <i class="bi bi-info-circle-fill nav-icon" aria-hidden="true"></i>
                            <span>Tentang</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-agro" href="{{ route('home') }}#kontak">
                            <i class="bi bi-chat-dots-fill nav-icon" aria-hidden="true"></i>
                            <span>Kontak</span>
                        </a>
                    </li>
                    @auth
                    <li class="nav-item nav-cta-item">
                        <a class="btn btn-nav-cta" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-fill" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

<style>
/* ===== BASE ===== */
body { padding-top: 70px; }

/* ===== NAVBAR ===== */
.navbar-agro {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(45, 106, 79, 0.08);
    padding: 0;
    height: 70px;
    transition: background 0.3s ease, box-shadow 0.3s ease, height 0.3s ease;
}

.navbar-agro.scrolled {
    background: rgba(255, 255, 255, 0.97);
    box-shadow: 0 1px 0 rgba(0,0,0,0.06), 0 4px 24px rgba(45, 106, 79, 0.08);
    height: 64px;
}

.navbar-agro .container {
    height: 100%;
    display: flex;
    align-items: center;
}

/* ===== BRAND ===== */
.navbar-brand {
    text-decoration: none;
    padding: 0;
}

.brand-logo-wrap {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    overflow: hidden;
    background: rgba(45, 106, 79, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.logo-navbar {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.brand-name {
    font-size: 1.1rem;
    color: var(--agro-text);
    letter-spacing: -0.02em;
    line-height: 1;
}

/* ===== NAV LINKS ===== */
.navbar-nav {
    gap: 2px;
}

.nav-link-agro {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--agro-text-muted) !important;
    padding: 0.5rem 0.875rem !important;
    border-radius: 8px;
    transition: color 0.2s ease, background 0.2s ease;
    white-space: nowrap;
}

.nav-icon {
    font-size: 0.8rem;
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.nav-link-agro:hover {
    color: var(--agro-primary) !important;
    background: rgba(45, 106, 79, 0.07);
}

.nav-link-agro:hover .nav-icon,
.nav-link-agro.active .nav-icon {
    opacity: 1;
}

.nav-link-agro.active {
    color: var(--agro-primary) !important;
    background: rgba(45, 106, 79, 0.09);
    font-weight: 600;
}

/* ===== CTA BUTTON ===== */
.nav-cta-item {
    margin-left: 6px;
}

.btn-nav-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--agro-primary);
    color: #fff !important;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.5rem 1.1rem;
    border-radius: 8px;
    text-decoration: none;
    border: none;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    min-height: 38px;
}

.btn-nav-cta:hover {
    background: var(--agro-primary-light);
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(45, 106, 79, 0.3);
}

/* ===== TOGGLER ===== */
.navbar-toggler {
    padding: 8px;
    border-radius: 8px;
    background: transparent;
    transition: background 0.2s ease;
}

.navbar-toggler:hover { background: rgba(45, 106, 79, 0.07); }
.navbar-toggler:focus { box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.18); outline: none; }

.toggler-icon {
    display: flex;
    flex-direction: column;
    gap: 5px;
    width: 20px;
}

.toggler-icon span {
    display: block;
    height: 2px;
    background: var(--agro-text);
    border-radius: 2px;
    transition: transform 0.28s ease, opacity 0.28s ease;
    transform-origin: center;
}

.navbar-toggler[aria-expanded="true"] .toggler-icon span:nth-child(1) {
    transform: translateY(7px) rotate(45deg);
}
.navbar-toggler[aria-expanded="true"] .toggler-icon span:nth-child(2) {
    opacity: 0;
    transform: scaleX(0);
}
.navbar-toggler[aria-expanded="true"] .toggler-icon span:nth-child(3) {
    transform: translateY(-7px) rotate(-45deg);
}

/* ===== MOBILE ===== */
@media (max-width: 991.98px) {
    body { padding-top: 70px; }

    .navbar-agro { height: 70px; }

    .navbar-collapse {
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(45, 106, 79, 0.1);
        padding: 0.75rem 1rem 1rem;
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    }

    .navbar-nav { gap: 2px; }

    .nav-link-agro {
        padding: 0.75rem 1rem !important;
        font-size: 0.95rem;
        border-radius: 10px;
    }

    .nav-icon { font-size: 0.9rem; }

    .nav-cta-item { margin-left: 0; margin-top: 0.5rem; }

    .btn-nav-cta {
        width: 100%;
        justify-content: center;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-size: 0.95rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var navbar = document.getElementById('mainNavbar');

    // Scroll effect
    function onScroll() {
        navbar.classList.toggle('scrolled', window.scrollY > 10);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Active link on scroll
    var sections = document.querySelectorAll('section[id]');
    var navLinks = document.querySelectorAll('.nav-link-agro');

    if (sections.length > 0) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    navLinks.forEach(function (link) {
                        var href = link.getAttribute('href') || '';
                        link.classList.toggle('active', href.includes('#' + entry.target.id));
                    });
                }
            });
        }, { rootMargin: '-30% 0px -60% 0px' });

        sections.forEach(function (s) { io.observe(s); });
    }

    // Close mobile menu on link click
    navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            var collapse = document.getElementById('navbarNav');
            if (collapse && collapse.classList.contains('show')) {
                var toggler = document.querySelector('.navbar-toggler');
                if (toggler) toggler.click();
            }
        });
    });
});
</script>
