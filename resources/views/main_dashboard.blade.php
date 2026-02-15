@include('layouts_backend.header')
<!-- Navigasi -->
@include('layouts_backend.nav')
<!-- Sidebar -->
@include('layouts_backend.sidebar')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  
    <!-- Content -->
    @yield('content')

    <!-- /.content -->
</div>
<!-- Footer -->
@include('layouts_backend.footer')
