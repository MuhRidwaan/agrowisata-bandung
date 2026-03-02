@extends('backend.main_dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Vendor Revenue Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vendor Revenue</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter & Export</h3>
                <div class="card-tools">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.vendor_revenue') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search Vendor</label>
                                <input type="text" name="search" class="form-control" placeholder="Vendor name..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                                    <a href="{{ route('reports.vendor_revenue') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @forelse($vendorRevenue as $vendor)
        <div class="card card-outline card-success mb-4 shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title text-bold">
                        <i class="fas fa-store mr-2 text-primary"></i> {{ $vendor['name'] }}
                    </h3>
                    <h4 class="mb-0 text-success font-weight-bold">
                        Total Revenue: Rp {{ number_format($vendor['total_revenue'], 0, ',', '.') }}
                    </h4>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4 py-2" style="width: 50%">Package Name</th>
                                <th class="text-center py-2">Total Bookings (Paid)</th>
                                <th class="text-right pr-4 py-2">Revenue Contribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendor['package_details'] as $package)
                            <tr>
                                <td class="pl-4">{{ $package['name'] }}</td>
                                <td class="text-center">{{ $package['bookings_count'] }}</td>
                                <td class="text-right pr-4 text-bold">Rp {{ number_format($package['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted small">No active packages for this vendor</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($vendor['total_revenue'] > 0)
                        <tfoot class="bg-agro-light">
                            <tr>
                                <th colspan="2" class="text-right py-2">Vendor Total:</th>
                                <th class="text-right pr-4 py-2 text-success">
                                    Rp {{ number_format($vendor['total_revenue'], 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i> No vendor revenue records found for the selected period.
        </div>
        @endforelse

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $vendorRevenue->links('pagination::bootstrap-4') }}
        </div>
    </div>
</section>

<style>
    .bg-agro-light {
        background-color: rgba(40, 167, 69, 0.05);
    }
</style>
@endsection
