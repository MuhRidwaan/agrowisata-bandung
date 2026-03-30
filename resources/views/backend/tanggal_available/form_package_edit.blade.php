@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Available Date By Package</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tanggal-available.index') }}">Available Date</a></li>
                    <li class="breadcrumb-item active">Edit By Package</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-body">

                <form method="POST" action="{{ route('tanggal-available.update-package', $paketTour->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Tour Package</label>
                        <input type="text" class="form-control" value="{{ $paketTour->nama_paket }}" readonly>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Start Date <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="tanggal_mulai"
                                           id="tanggal_mulai"
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                           value="{{ old('tanggal_mulai', $summary->tanggal_awal) }}"
                                           required>
                                    @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai">End Date <span class="text-danger">*</span></label>
                                    <input type="date"
                                           name="tanggal_selesai"
                                           id="tanggal_selesai"
                                           class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                           value="{{ old('tanggal_selesai', $summary->tanggal_akhir) }}"
                                           required>
                                    @error('tanggal_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kuota">Default Quota (Optional)</label>
                        <input type="number"
                               name="kuota"
                               id="kuota"
                               min="1"
                               class="form-control @error('kuota') is-invalid @enderror"
                               value="{{ old('kuota') }}"
                               placeholder="Fill to apply default quota to generated rows">
                        @error('kuota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            You can still edit quota per row after generating rows.
                        </small>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-info" id="btn-generate-dates">
                            <i class="fas fa-calendar-plus mr-1"></i> Generate Date Rows
                        </button>
                        <small id="generate-error" class="form-text text-danger d-none"></small>
                    </div>

                    <div class="form-group">
                        <label>Quota Per Date <span class="text-danger">*</span></label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th width="6%">No</th>
                                        <th width="22%">Date</th>
                                        <th width="20%">Day</th>
                                        <th width="20%">Quota</th>
                                        <th>Current Status</th>
                                    </tr>
                                </thead>
                                <tbody id="dates-table-body">
                                </tbody>
                            </table>
                        </div>
                        @error('dates')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="status">Status (Optional)</label>
                        <select name="status"
                                id="status"
                                class="form-control @error('status') is-invalid @enderror">
                            <option value="">-- Do Not Change Status --</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Active</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Leave empty if you only want to update quota per date row.
                        </small>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('tanggal-available.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </form>

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-light">
                <strong>Current Summary</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2 mb-md-0">
                        <small class="text-muted d-block">Total Dates</small>
                        <strong>{{ (int) $summary->total_dates }} Days</strong>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <small class="text-muted d-block">Date Range</small>
                        <strong>{{ $summary->tanggal_awal }} to {{ $summary->tanggal_akhir }}</strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Total Quota</small>
                        <strong>{{ number_format((int) $summary->total_kuota) }}</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection

@php
    $allDatesForJs = $dates->map(function ($row) {
        return [
            'id' => $row->id,
            'tanggal' => $row->tanggal,
            'kuota' => $row->kuota,
            'status' => $row->status,
        ];
    })->values();
@endphp

@push('scripts')
<script>
    (function () {
        const startInput = document.getElementById('tanggal_mulai');
        const endInput = document.getElementById('tanggal_selesai');
        const defaultQuotaInput = document.getElementById('kuota');
        const generateButton = document.getElementById('btn-generate-dates');
        const tableBody = document.getElementById('dates-table-body');
        const generateError = document.getElementById('generate-error');

        const allDates = @json($allDatesForJs);
        const oldDates = @json(old('dates', []));

        function clearRows() {
            tableBody.innerHTML = '';
        }

        function dayName(dateString) {
            const date = new Date(`${dateString}T00:00:00`);
            return date.toLocaleDateString('en-US', { weekday: 'long' });
        }

        function statusBadge(status) {
            return status === 'aktif'
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';
        }

        function showGenerateError(message) {
            generateError.textContent = message;
            generateError.classList.remove('d-none');
        }

        function clearGenerateError() {
            generateError.textContent = '';
            generateError.classList.add('d-none');
        }

        function buildRow(index, row, quotaValue) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>
                    ${row.tanggal}
                    <input type="hidden" name="dates[${index}][id]" value="${row.id}">
                </td>
                <td>${dayName(row.tanggal)}</td>
                <td>
                    <input type="number"
                           min="1"
                           name="dates[${index}][kuota]"
                           class="form-control"
                           value="${quotaValue}"
                           required>
                </td>
                <td>${statusBadge(row.status)}</td>
            `;
            return tr;
        }

        function renderRows(rows, oldMap = {}) {
            clearRows();
            const defaultQuota = parseInt(defaultQuotaInput.value, 10);
            rows.forEach((row, index) => {
                const quotaFromOld = oldMap[row.id];
                const quota = Number.isInteger(defaultQuota) && defaultQuota > 0
                    ? defaultQuota
                    : (quotaFromOld || row.kuota);
                tableBody.appendChild(buildRow(index, row, quota));
            });
        }

        function filterRowsByRange() {
            const start = startInput.value;
            const end = endInput.value;
            clearGenerateError();
            if (!start || !end) {
                showGenerateError('Please fill Start Date and End Date.');
                return;
            }
            if (new Date(start) > new Date(end)) {
                showGenerateError('End Date must be greater than or equal to Start Date.');
                return;
            }

            const filtered = allDates.filter((row) => row.tanggal >= start && row.tanggal <= end);
            if (filtered.length === 0) {
                showGenerateError('No existing rows found in the selected date range.');
                clearRows();
                return;
            }

            renderRows(filtered);
        }

        if (oldDates.length > 0) {
            const oldMap = {};
            oldDates.forEach((row) => {
                oldMap[parseInt(row.id, 10)] = parseInt(row.kuota, 10);
            });
            const oldIds = oldDates.map((row) => parseInt(row.id, 10));
            const restored = allDates.filter((row) => oldIds.includes(parseInt(row.id, 10)));
            renderRows(restored, oldMap);
        } else {
            filterRowsByRange();
        }

        generateButton.addEventListener('click', filterRowsByRange);
    })();
</script>
@endpush
