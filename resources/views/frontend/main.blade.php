<!-- Navigasi -->
@section('header')
    @include('frontend.layouts.header')
    @include('frontend.layouts.nav')
@show

<!-- Content -->
<main id="main-content">
    @yield('content')
</main>

<!-- Footer -->
@include('frontend.layouts.footer')
