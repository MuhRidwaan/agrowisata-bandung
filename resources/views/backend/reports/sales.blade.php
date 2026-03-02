@extends('backend.main_dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Sales Report</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Sales Report</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Summary Cards -->
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $payments->count() }}</h3>
                        <p>Successful Payments</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter & Export</h3>
                <div class="card-tools">
                    <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.sales') }}" method="GET">
                    <div class="row">
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
                                    <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title"><i class="fas fa-list mr-1"></i> Sales History</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Booking Code</th>
                            <th>Customer Detail</th>
                            <th>Tour Package</th>
                            <th>Pax</th>
                            <th class="text-right">Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i') }}</td>
                            <td><code>{{ $payment->booking->booking_code }}</code></td>
                            <td>
                                <strong>{{ $payment->booking->customer_name }}</strong><br>
                                <small class="text-muted">
                                    <i class="fas fa-envelope mr-1"></i>{{ $payment->booking->customer_email }}<br>
                                    <i class="fas fa-phone mr-1"></i>{{ $payment->booking->customer_phone }}
                                </small>
                            </td>
                            <td>{{ $payment->booking->paketTour->nama_paket ?? '-' }}</td>
                            <td>{{ $payment->booking->jumlah_peserta }}</td>
                            <td class="text-right">Rp {{ number_format($payment->booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No sales records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $payments->links('pagination::bootstrap-4') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
