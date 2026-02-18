@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <h1>Report</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            {{-- ================= FILTER ================= --}}
            <form method="GET" class="mb-3">
                <div class="d-flex gap-2">
                    <input type="date" name="start" class="form-control" style="max-width:200px;">
                    <input type="date" name="end" class="form-control" style="max-width:200px;">
                    <button class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>

            {{-- ================= SUMMARY ================= --}}
            <div class="row">

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $totalVendor }}</h3>
                            <p>Total Vendor</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalReview }}</h3>
                            <p>Total Review</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($avgRating, 1) }}</h3>
                            <p>Average Rating</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $topVendor->name ?? '-' }}</h3>
                            <p>Top Vendor</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ================= CHART ================= --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chart Review Vendor</h3>
                </div>

                <div class="card-body">
                    <canvas id="chartReview"></canvas>
                </div>
            </div>

            {{-- ================= AREA REPORT ================= --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Report per Area</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Area</th>
                            <th>Jumlah Vendor</th>
                        </tr>

                        @foreach ($areaData as $area)
                            <tr>
                                <td>{{ $area->name }}</td>
                                <td>{{ $area->vendors_count }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            {{-- ================= EXPORT ================= --}}
            <div class="mb-3">
                <a href="{{ route('report.pdf') }}" class="btn btn-danger btn-sm">Export PDF</a>
                <a href="{{ route('report.excel') }}" class="btn btn-success btn-sm">Export Excel</a>
            </div>

        </div>
    </section>

    {{-- ================= CHART JS ================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('chartReview');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData->pluck('name')) !!},
                datasets: [{
                    label: 'Jumlah Review',
                    data: {!! json_encode($chartData->pluck('reviews_count')) !!}
                }]
            }
        });
    </script>
@endsection
