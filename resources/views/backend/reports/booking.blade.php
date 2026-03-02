@extends('backend.main_dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Booking Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Booking Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistics Summary -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-info shadow">
                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                    <div class="inner">
                        <span class="info-box-text">Total Bookings</span>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-warning shadow">
                    <span class="info-box-icon text-white"><i class="fas fa-clock"></i></span>
                    <div class="inner">
                        <span class="info-box-text">Pending</span>
                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-success shadow">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="inner">
                        <span class="info-box-text">Paid</span>
                        <h3 class="mb-0">{{ $stats['paid'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box bg-danger shadow">
                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                    <div class="inner">
                        <span class="info-box-text">Cancelled</span>
                        <h3 class="mb-0">{{ $stats['cancelled'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

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
                <form action="{{ route('reports.booking') }}" method="GET">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">-- All Status --</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Per Page</label>
                                <select name="per_page" class="form-control" onchange="this.form.submit()">
                                    @foreach([10, 25, 50, 100] as $val)
                                        <option value="{{ $val }}" {{ request('per_page') == $val ? 'selected' : '' }}>{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Filter</button>
                                    <a href="{{ route('reports.booking') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-list mr-1"></i> Booking Data</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date Created</th>
                            <th>Booking Code</th>
                            <th>Customer Name</th>
                            <th>Tour Package</th>
                            <th>Pax</th>
                            <th>Status</th>
                            <th class="text-right">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $index => $booking)
                        <tr>
                            <td>{{ $bookings->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i') }}</td>
                            <td><code>{{ $booking->booking_code }}</code></td>
                            <td>{{ $booking->customer_name }}</td>
                            <td>{{ $booking->paketTour->nama_paket ?? '-' }}</td>
                            <td>{{ $booking->jumlah_peserta }}</td>
                            <td>
                                @php
                                    $badge = match ($booking->status) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badge }}">{{ strtoupper($booking->status) }}</span>
                            </td>
                            <td class="text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">No booking records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $bookings->links('pagination::bootstrap-4') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
