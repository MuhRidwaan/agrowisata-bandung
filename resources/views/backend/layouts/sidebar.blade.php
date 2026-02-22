<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity:.8">
        <span class="brand-text font-weight-light">Agrowisata</span>
    </a>

    <div class="sidebar">

        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    {{ Auth::user()->name }}
                </a>
            </div>
        </div>

        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">MASTER DATA</li>

                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>User Management</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('roles.index') }}"
                        class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Role & Permission</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('vendors.index') }}"
                        class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Seller / Vendor</p>
                    </a>
                </li>

                @php
                    $isPaketTourActive =
                        request()->routeIs('paket-tours.*') ||
                        request()->routeIs('paket-tour-photos.*') ||
                        request()->routeIs('pricing-tiers.*') ||
                        request()->routeIs('tanggal-available.*');
                @endphp

                <li class="nav-item {{ $isPaketTourActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isPaketTourActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marked-alt"></i>
                        <p>
                            Paket Tour
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('paket-tours.index') }}"
                                class="nav-link {{ request()->routeIs('paket-tours.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Paket</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('paket-tour-photos.index') }}"
                                class="nav-link {{ request()->routeIs('paket-tour-photos.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gallery Foto</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pricing-tiers.index') }}"
                                class="nav-link {{ request()->routeIs('pricing-tiers.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pricing Tier</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('tanggal-available.index') }}"
                                class="nav-link {{ request()->routeIs('tanggal-available.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tanggal Available</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">TRANSACTION</li>

                <li class="nav-item">
                    <a href="{{ route('bookings.index') }}"
                        class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Booking</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('payments.index') }}"
                        class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payment</p>
                    </a>
                </li>

                <li class="nav-header">USER ACTIVITY</li>

                <li class="nav-item">
                    <a href="{{ route('review.index') }}"
                        class="nav-link {{ request()->routeIs('review.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-star"></i>
                        <p>Review & Rating</p>
                    </a>
                </li>

                <li class="nav-header">CONFIG</li>

                <li class="nav-item">
                    <a href="{{ route('whatsappsetting.index') }}"
                        class="nav-link {{ request()->routeIs('whatsappsetting.*') ? 'active' : '' }}">
                        <i class="nav-icon fab fa-whatsapp"></i>
                        <p>WhatsApp Setting</p>
                    </a>
                </li>

                <li class="nav-header">REPORT</li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Report</p>
                    </a>
                </li>

            </ul>
        </nav>

    </div>
</aside>
