@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Financial Audit Trail</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item">Reports</li>
                        <li class="breadcrumb-item active">Transaction Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Filter Card -->
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h3 class="card-title text-sm"><i class="fas fa-filter"></i> Filter Logs</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.transaction_logs') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Booking Code</label>
                                    <input type="text" name="search" class="form-control" placeholder="Search code..." value="{{ request('search') }}">
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
                                    <label>Action Type</label>
                                    <select name="action" class="form-control">
                                        <option value="">All Actions</option>
                                        <option value="booking_created" {{ request('action') == 'booking_created' ? 'selected' : '' }}>New Booking (Backend)</option>
                                        <option value="booking_created_frontend" {{ request('action') == 'booking_created_frontend' ? 'selected' : '' }}>New Booking (Frontend)</option>
                                        <option value="payment_callback_success" {{ request('action') == 'payment_callback_success' ? 'selected' : '' }}>Payment Success (Midtrans)</option>
                                        <option value="payment_callback_failed" {{ request('action') == 'payment_callback_failed' ? 'selected' : '' }}>Payment Failed (Midtrans)</option>
                                        <option value="payment_confirmed_manual" {{ request('action') == 'payment_confirmed_manual' ? 'selected' : '' }}>Manual Confirmation</option>
                                        <option value="status_updated" {{ request('action') == 'status_updated' ? 'selected' : '' }}>Status Update</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('reports.transaction_logs') }}" class="btn btn-secondary btn-sm mr-2">Reset</a>
                            <button type="submit" class="btn btn-primary btn-sm">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Detailed Transaction History</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-striped">
                        <thead class="bg-light text-sm">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Timestamp</th>
                                <th width="12%">Booking Code</th>
                                <th width="12%">Actor</th>
                                <th width="12%">Action</th>
                                <th width="15%">Status Change</th>
                                <th width="12%">Amount</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @forelse ($logs as $log)
                                <tr>
                                    <td class="text-center">{{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $log->booking->booking_code ?? 'Deleted' }}</span>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="text-primary"><i class="fas fa-user-circle"></i> {{ $log->user->name }}</span>
                                        @else
                                            <span class="text-muted"><i class="fas fa-robot"></i> System / Callback</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="font-weight-bold">{{ strtoupper(str_replace('_', ' ', $log->action)) }}</small>
                                    </td>
                                    <td>
                                        @if($log->old_status || $log->new_status)
                                            <span class="text-muted">{{ $log->old_status ?? 'Start' }}</span> 
                                            <i class="fas fa-arrow-right mx-1 text-xs"></i> 
                                            <span class="font-weight-bold">{{ $log->new_status ?? 'N/A' }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->amount)
                                            Rp {{ number_format($log->amount, 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $log->description }}
                                        @if($log->payment_method)
                                            <br><small class="text-muted">Method: {{ strtoupper($log->payment_method) }}</small>
                                        @endif
                                        @if($log->payload)
                                            <button type="button" class="btn btn-link btn-xs p-0 text-info ml-1" 
                                                    onclick="showPayload({{ $log->id }})">
                                                (View Raw Data)
                                            </button>
                                            <div id="payload-{{ $log->id }}" style="display:none;">{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No transaction logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal for JSON Payload -->
    <div class="modal fade" id="payloadModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-white">Raw Transaction Data (JSON)</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <pre id="jsonContent" class="bg-light p-3 m-0" style="max-height: 500px; overflow-y: auto;"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPayload(id) {
            const content = document.getElementById('payload-' + id).innerText;
            document.getElementById('jsonContent').innerText = content;
            $('#payloadModal').modal('show');
        }
    </script>
@endsection
