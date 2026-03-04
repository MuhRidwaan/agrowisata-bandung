<!-- Navigasi -->
@if(!isset($hideHeader))
    @include('frontend.layouts.header')
    @include('frontend.layouts.nav')
@endif
<!-- Content -->
@yield('content')

<!-- /.content -->
<!-- Footer -->
@include('frontend.layouts.footer')

@stack('scripts')