@extends('backend..main_dashboard')

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
                                class="form-control"
                                required>

                            <option value="">-- Select Tour Package --</option>

                            @foreach ($paketTours as $paket)
                                <option value="{{ $paket->id }}"
                                    {{ old('paket_tour_id', $tanggalAvailable->paket_tour_id ?? '') == $paket->id ? 'selected' : '' }}>
                                    {{ $paket->nama_paket }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Date --}}
                    <div class="form-group">
                        <label for="tanggal">
                            Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="tanggal"
                               id="tanggal"
                               class="form-control"
                               value="{{ old('tanggal', $tanggalAvailable->tanggal ?? '') }}"
                               required>
                    </div>

                    {{-- Quota --}}
                    <div class="form-group">
                        <label for="kuota">
                            Quota <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="kuota"
                               id="kuota"
                               class="form-control"
                               min="1"
                               value="{{ old('kuota', $tanggalAvailable->kuota ?? '') }}"
                               required
                               placeholder="Minimum 1">
                    </div>

                    {{-- Status --}}
                    <div class="form-group">
                        <label for="status">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select name="status"
                                id="status"
                                class="form-control"
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
                    </div>

                    {{-- Buttons --}}
                    <div class="mt-3">
                        <button type="submit"
                                class="btn btn-primary">
                            {{ $edit ? 'Update' : 'Save' }}
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