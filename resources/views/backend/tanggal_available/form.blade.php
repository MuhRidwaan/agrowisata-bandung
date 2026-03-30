@extends('backend.main_dashboard')

@section('content')

{{-- Content Header --}}
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>
                    {{ $edit ? 'Edit Available Date' : 'Add Available Date' }}
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('tanggal-available.index') }}">
                            Available Date
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $edit ? 'Edit' : 'Add' }} Available Date
                    </li>
                </ol>
            </div>

        </div>
    </div>
</section>


{{-- Main Content --}}
<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-body">

                <form method="POST"
                      action="{{ $edit
                          ? route('tanggal-available.update', $tanggalAvailable)
                          : route('tanggal-available.store') }}">

                    @csrf

                    @if ($edit)
                        @method('PUT')
                    @endif

                    {{-- Tour Package --}}
                    <div class="form-group">
                        <label for="paket_tour_id">
                            Tour Package <span class="text-danger">*</span>
                        </label>

                        <select name="paket_tour_id"
                                id="paket_tour_id"
                                class="form-control @error('paket_tour_id') is-invalid @enderror"
                                required>

                            <option value="">-- Select Tour Package --</option>

                            @foreach ($paketTours as $paket)
                                <option value="{{ $paket->id }}"
                                    {{ old('paket_tour_id', $tanggalAvailable->paket_tour_id ?? '') == $paket->id ? 'selected' : '' }}>
                                    {{ $paket->nama_paket }}
                                </option>
                            @endforeach
                        </select>
                        @error('paket_tour_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if ($edit)
                        {{-- Date (Edit single date) --}}
                        <div class="form-group">
                            <label for="tanggal">
                                Date <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="tanggal"
                                   id="tanggal"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   value="{{ old('tanggal', $tanggalAvailable->tanggal ?? '') }}"
                                   required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        {{-- Date Range (Create bulk dates) --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai">
                                        Start Date <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                           name="tanggal_mulai"
                                           id="tanggal_mulai"
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                           value="{{ old('tanggal_mulai') }}"
                                           required>
                                    @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai">
                                        End Date
                                    </label>

                                    <input type="date"
                                           name="tanggal_selesai"
                                           id="tanggal_selesai"
                                           class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                           value="{{ old('tanggal_selesai') }}">
                                    @error('tanggal_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Fill start and end date to generate daily available dates automatically (for months or up to 1 year). Leave end date empty to add only one date.
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Quota --}}
                    <div class="form-group">
                        <label for="kuota">
                            {{ $edit ? 'Quota' : 'Default Quota (Optional)' }}
                            @if ($edit)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        <input type="number"
                               name="kuota"
                               id="kuota"
                               class="form-control @error('kuota') is-invalid @enderror"
                               min="1"
                               value="{{ old('kuota', $tanggalAvailable->kuota ?? '') }}"
                               {{ $edit ? 'required' : '' }}
                               placeholder="Minimum 1">
                        @error('kuota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if (!$edit)
                            <small class="form-text text-muted">
                                This value will be used as default, then you can edit quota per day in the generated table below.
                            </small>
                        @endif
                    </div>

                    @if (!$edit)
                        <div class="form-group">
                            <button type="button" class="btn btn-info" id="btn-generate-dates">
                                <i class="fas fa-calendar-plus mr-1"></i> Generate Date Rows
                            </button>
                            <small id="generate-error" class="form-text text-danger d-none"></small>
                        </div>

                        <div id="dates-card" class="card border {{ old('dates') ? '' : 'd-none' }}">
                            <div class="card-header bg-light py-2">
                                <strong>Available Dates Detail</strong>
                                <small class="text-muted d-block">Edit quota per date before save.</small>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th width="6%">No</th>
                                                <th width="25%">Date</th>
                                                <th width="25%">Day</th>
                                                <th>Quota</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dates-table-body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @error('dates')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    @endif

                    {{-- Status --}}
                    <div class="form-group">
                        <label for="status">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select name="status"
                                id="status"
                                class="form-control @error('status') is-invalid @enderror"
                                required>

                            <option value="aktif"
                                {{ old('status', $tanggalAvailable->status ?? '') == 'aktif' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="nonaktif"
                                {{ old('status', $tanggalAvailable->status ?? '') == 'nonaktif' ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="mt-3">
                        <button type="submit"
                                class="btn btn-primary">
                            {{ $edit ? 'Save' : 'Save' }}
                        </button>

                        <a href="{{ route('tanggal-available.index') }}"
                           class="btn btn-secondary">
                            Back
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</section>

@endsection

@if (!$edit)
@push('scripts')
<script>
    (function () {
        const startInput = document.getElementById('tanggal_mulai');
        const endInput = document.getElementById('tanggal_selesai');
        const defaultQuotaInput = document.getElementById('kuota');
        const generateButton = document.getElementById('btn-generate-dates');
        const tableBody = document.getElementById('dates-table-body');
        const datesCard = document.getElementById('dates-card');
        const generateError = document.getElementById('generate-error');

        const oldDates = @json(old('dates', []));

        function toDateOnly(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function formatDayName(dateString) {
            const date = new Date(`${dateString}T00:00:00`);
            return date.toLocaleDateString('en-US', { weekday: 'long' });
        }

        function clearRows() {
            tableBody.innerHTML = '';
        }

        function showCard(show) {
            datesCard.classList.toggle('d-none', !show);
        }

        function showGenerateError(message) {
            generateError.textContent = message;
            generateError.classList.remove('d-none');
        }

        function clearGenerateError() {
            generateError.textContent = '';
            generateError.classList.add('d-none');
        }

        function buildRow(index, dateString, quotaValue) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>
                    ${dateString}
                    <input type="hidden" name="dates[${index}][tanggal]" value="${dateString}">
                </td>
                <td>${formatDayName(dateString)}</td>
                <td>
                    <input type="number"
                           min="1"
                           class="form-control"
                           name="dates[${index}][kuota]"
                           value="${quotaValue}"
                           required>
                </td>
            `;
            return tr;
        }

        function renderRowsFromList(dateList, defaultQuota) {
            clearRows();
            dateList.forEach((dateString, index) => {
                tableBody.appendChild(buildRow(index, dateString, defaultQuota));
            });
            showCard(dateList.length > 0);
        }

        function generateRows() {
            const startValue = startInput.value;
            const endValue = endInput.value || startValue;
            const defaultQuota = parseInt(defaultQuotaInput.value, 10) || 1;
            clearGenerateError();

            if (!startValue) {
                showGenerateError('Please fill Start Date first.');
                return;
            }

            if (new Date(startValue) > new Date(endValue)) {
                showGenerateError('End Date must be greater than or equal to Start Date.');
                return;
            }

            const dates = [];
            const cursor = new Date(`${startValue}T00:00:00`);
            const end = new Date(`${endValue}T00:00:00`);

            while (cursor <= end) {
                dates.push(toDateOnly(cursor));
                cursor.setDate(cursor.getDate() + 1);
            }

            if (dates.length > 366) {
                showGenerateError('Maximum date range is 366 days.');
                return;
            }

            renderRowsFromList(dates, defaultQuota);
        }

        if (oldDates.length > 0) {
            clearRows();
            oldDates.forEach((row, index) => {
                const quota = parseInt(row.kuota, 10) || 1;
                tableBody.appendChild(buildRow(index, row.tanggal, quota));
            });
            showCard(true);
        }

        generateButton.addEventListener('click', generateRows);
    })();
</script>
@endpush
@endif
