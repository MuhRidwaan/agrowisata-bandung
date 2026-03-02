@extends('backend.main_dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Tour Performance Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Performance Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Top Selling Packages -->
            <div class="col-md-6">
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-trophy mr-1 text-warning"></i> Top 5 Best Selling Packages</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Package Name</th>
                                    <th class="text-center">Total Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPackages as $package)
                                <tr>
                                    <td>{{ $package->nama_paket }}</td>
                                    <td class="text-center"><span class="badge badge-success px-3">{{ $package->bookings_count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Area Popularity -->
            <div class="col-md-6">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-map-marked-alt mr-1 text-primary"></i> Area Popularity</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Area Name</th>
                                    <th class="text-center">Total Bookings</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($areaPopularity as $area)
                                <tr>
                                    <td>{{ $area->name }}</td>
                                    <td class="text-center"><span class="badge badge-info px-3">{{ $area->total_bookings }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Performance -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-store mr-1 text-primary"></i> Vendor Performance Analysis</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Vendor Name</th>
                                    <th class="text-center">Total Bookings (Paid)</th>
                                    <th class="text-right">Total Revenue Contribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendorPerformance as $vendor)
                                <tr>
                                    <td>{{ $vendor['name'] }}</td>
                                    <td class="text-center">{{ $vendor['total_bookings'] }}</td>
                                    <td class="text-right text-bold text-success">Rp {{ number_format($vendor['total_revenue'], 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">No vendor activity records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
