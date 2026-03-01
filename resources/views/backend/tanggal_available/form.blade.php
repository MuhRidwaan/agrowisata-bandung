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

                    {{-- Date --}}
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

                    {{-- Quota --}}
                    <div class="form-group">
                        <label for="kuota">
                            Quota <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="kuota"
                               id="kuota"
                               class="form-control @error('kuota') is-invalid @enderror"
                               min="1"
                               value="{{ old('kuota', $tanggalAvailable->kuota ?? '') }}"
                               required
                               placeholder="Minimum 1">
                        @error('kuota')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

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

        {{-- DETAIL VIEW-ONLY: All available dates for this package --}}
        @if ($edit && $details->count())
        <div class="card mt-3">
            <div class="card-header bg-light">
                <h3 class="card-title mb-0">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    All Available Dates for "{{ $tanggalAvailable->paketTour->nama_paket ?? '-' }}"
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Date</th>
                            <th>Quota</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                            <tr class="{{ $detail->id == $tanggalAvailable->id ? 'table-warning' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $detail->tanggal }}
                                    @if ($detail->id == $tanggalAvailable->id)
                                        <span class="badge badge-warning ml-1">Current</span>
                                    @endif
                                </td>
                                <td>{{ $detail->kuota }}</td>
                                <td>
                                    <span class="badge badge-{{ $detail->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($detail->status) }}
                                    </span>
                                </td>
                                <td>{{ $detail->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                Total: {{ $details->count() }} dates | Total Quota: {{ $details->sum('kuota') }}
            </div>
        </div>
        @endif

    </div>
</section>

@endsection