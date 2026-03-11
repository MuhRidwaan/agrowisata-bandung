<aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ setting_asset_url('app_logo') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity:.8; max-height: 33px;">
        <span class="brand-text font-weight-light">{{ get_setting('app_name', 'Jabar Agro') }}</span>
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

                @role('Super Admin')
                <li class="nav-header">MASTER DATA</li>

                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>User Management</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('vendors.index') }}"
                        class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-store"></i>
                        <p>Seller / Vendor</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('areas.index') }}"
                        class="nav-link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Area</p>
                    </a>
                </li>
                @endrole
                
                @php
                    $isPaketTourActive =
                        request()->routeIs('paket-tours.*') ||
                        request()->routeIs('paket-tour-photos.*') ||
                        request()->routeIs('pricingtiers.*') ||
                        request()->routeIs('tanggal-available.*') ||
                        request()->routeIs('pricingrules.*');
                @endphp

                <li class="nav-item {{ $isPaketTourActive ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $isPaketTourActive ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marked-alt"></i>
                        <p>
                            Tour Package
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('paket-tours.index') }}"
                                class="nav-link {{ request()->routeIs('paket-tours.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tour Package Data</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('paket-tour-photos.index') }}"
                                class="nav-link {{ request()->routeIs('paket-tour-photos.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Gallery Photo</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('pricingrules.index') }}"
                                class="nav-link {{ request()->routeIs('pricingrules.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pricing Rules</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="{{ route('pricingtiers.index') }}"
                                class="nav-link {{ request()->routeIs('pricingtiers.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pricing Tier</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="{{ route('tanggal-available.index') }}"
                                class="nav-link {{ request()->routeIs('tanggal-available.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Available Date</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">TRANSACTIONS</li>

                <li class="nav-item">
                    <a href="{{ route('bookings.index') }}"
                        class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Bookings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('payments.index') }}"
                        class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Payments</p>
                    </a>
                </li>

                <li class="nav-header">USER ACTIVITY</li>

                <li class="nav-item">
                    <a href="{{ route('review.index') }}"
                        class="nav-link {{ request()->routeIs('review.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-star"></i>
                        <p>Reviews & Ratings</p>
                    </a>
                </li>

                @role('Super Admin')
                <li class="nav-header">CONFIGURATION</li>

                <li class="nav-item">
                    <a href="{{ route('whatsappsetting.index') }}"
                        class="nav-link {{ request()->routeIs('whatsappsetting.*') ? 'active' : '' }}">
                        <i class="nav-icon fab fa-whatsapp"></i>
                        <p>WhatsApp Settings</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('settings.index') }}"
                        class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Global Settings</p>
                    </a>
                </li>
                @endrole

                <li class="nav-header">REPORTS</li>

                <li class="nav-item {{ request()->routeIs('reports.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Sales Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.booking') }}" class="nav-link {{ request()->routeIs('reports.booking') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Booking Report</p>
                            </a>
                        </li>
                        @role('Super Admin')
                        <li class="nav-item">
                            <a href="{{ route('reports.user') }}" class="nav-link {{ request()->routeIs('reports.user') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User Report</p>
                            </a>
                        </li>
                        @endrole
                        <li class="nav-item">
                            <a href="{{ route('reports.performance') }}" class="nav-link {{ request()->routeIs('reports.performance') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Performance Report</p>
                            </a>
                        </li>
                        @role('Super Admin')
                        <li class="nav-item">
                            <a href="{{ route('reports.vendor_revenue') }}" class="nav-link {{ request()->routeIs('reports.vendor_revenue') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vendor Revenue</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('reports.transaction_logs') }}" class="nav-link {{ request()->routeIs('reports.transaction_logs') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Financial Audit Trail</p>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </li>

            </ul>
        </nav>

    </div>
</aside>
