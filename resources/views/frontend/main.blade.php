<!-- Navigasi -->
@section('header')
    @include('frontend.layouts.header')
    @include('frontend.layouts.nav')
@show
<!-- Content -->
@yield('content')

<!-- /.content -->
<!-- Footer -->
@include('frontend.layouts.footer')

@stack('scripts')