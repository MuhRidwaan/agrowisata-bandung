@extends('backend.main_dashboard')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($tier->id) ? 'Edit Pricing Tier' : 'Tambah Pricing Tier' }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('pricing-tiers.index') }}">Pricing Tier</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($tier->id) ? 'Edit' : 'Tambah' }} Pricing Tier
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ isset($tier->id) ? 'Form Edit Pricing Tier' : 'Form Tambah Pricing Tier' }}
                            </h3>
                        </div>
                        <form method="POST"
                            action="{{ isset($tier->id) ? route('pricing-tiers.update', $tier->id) : route('pricing-tiers.store') }}">
                            @csrf
                            @if (isset($tier->id))
                                @method('PUT')
                            @endif
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="paket_tour_id">Paket Tour</label>
                                    <select name="paket_tour_id" id="paket_tour_id" class="form-control" required>
                                        <option value="">-- Pilih Paket Tour --</option>
                                        @foreach ($paketTours as $paket)
                                            <option value="{{ $paket->id }}"
                                                {{ old('paket_tour_id', $tier->paket_tour_id) == $paket->id ? 'selected' : '' }}>
                                                {{ $paket->nama_paket }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="qty_min">Qty Min</label>
                                    <input type="number" class="form-control" id="qty_min" name="qty_min"
                                        value="{{ old('qty_min', $tier->qty_min) }}" min="1" required
                                        placeholder="Minimal 1">
                                </div>
                                <div class="form-group">
                                    <label for="qty_max">Qty Max</label>
                                    <input type="number" class="form-control" id="qty_max" name="qty_max"
                                        value="{{ old('qty_max', $tier->qty_max) }}" min="1" required
                                        placeholder="Minimal 1">
                                </div>
                                <div class="form-group">
                                    <label for="harga">Harga</label>
                                    <input type="number" class="form-control" id="harga" name="harga"
                                        value="{{ old('harga', $tier->harga) }}" min="0" step="1000" required
                                        placeholder="Contoh: 100000">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit"
                                    class="btn btn-primary">{{ isset($tier->id) ? 'Update' : 'Simpan' }}</button>
                                <a href="{{ route('pricing-tiers.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
