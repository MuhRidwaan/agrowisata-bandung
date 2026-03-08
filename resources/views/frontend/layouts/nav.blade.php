<body>
    <!-- Skip to main content for accessibility -->
    <a href="#destinasi" class="visually-hidden-focusable skip-link">Langsung ke konten utama</a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-agro fixed-top py-3" role="navigation" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <i class="bi bi-leaf text-primary-agro"></i>
                <img src="{{ get_setting('app_logo') ? asset('storage/' . get_setting('app_logo')) : asset('frontend/img/logo.png') }}" 
                     alt="AgroBandung Logo"
                     style="height:42px; width:auto; transform: scale(1.8); transform-origin:left center;">
                <span class="font-display fw-bold fs-5" style="margin-left:23px;">{{ get_setting('app_name', 'AgroBandung') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#destinasi">Destinasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#tentang">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#kontak">Kontak</a></li>
                </ul>
            </div>
        </div>
    </nav>

 


