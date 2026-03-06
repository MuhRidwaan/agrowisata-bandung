@extends('backend.main_dashboard')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ __('Profile') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <!-- Update Profile Info Card -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Profile Information') }}</h3>
                    </div>
                    <!-- /.card-header -->
                    @include('backend.profile.partials.update-profile-information-form')
                </div>
                <!-- /.card -->

                {{-- 
                <!-- Delete Account Card -->
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Delete Account') }}</h3>
                    </div>
                    <!-- /.card-header -->
                    @include('backend.profile.partials.delete-user-form')
                </div>
                <!-- /.card -->
                --}}
            </div>
            
            <div class="col-md-6">
                <!-- Update Password Card -->
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Update Password') }}</h3>
                    </div>
                    <!-- /.card-header -->
                    @include('backend.profile.partials.update-password-form')
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
@endsection
