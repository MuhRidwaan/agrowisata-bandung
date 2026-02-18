@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">

        <h4 class="mb-3">{{ isset($rule) ? 'Edit Rule' : 'Tambah Rule Diskon' }}</h4>

        <div class="card">
            <div class="card-body">

                <form method="POST"
                    action="{{ isset($rule) ? route('pricing-rules.update', $rule->id) : route('pricing-rules.store') }}">
                    @csrf
                    @if (isset($rule))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label>Paket</label>
                        <select name="tour_package_id" class="form-control" required>
                            <option value="">-- pilih --</option>
                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('tour_package_id', $rule->tour_package_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Min Orang</label>
                            <input type="number" name="min_pax" class="form-control"
                                value="{{ old('min_pax', $rule->min_pax ?? '') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Max Orang</label>
                            <input type="number" name="max_pax" class="form-control"
                                value="{{ old('max_pax', $rule->max_pax ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Tipe Diskon</label>
                        <select name="discount_type" class="form-control">
                            <option value="percent"
                                {{ old('discount_type', $rule->discount_type ?? '') == 'percent' ? 'selected' : '' }}>Persen</option>
                            <option value="nominal"
                                {{ old('discount_type', $rule->discount_type ?? '') == 'nominal' ? 'selected' : '' }}>Nominal
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Nilai Diskon</label>
                        <input type="number" name="discount_value" class="form-control"
                            value="{{ old('discount_value', $rule->discount_value ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Keterangan</label>
                        <input type="text" name="description" class="form-control"
                            value="{{ old('description', $rule->description ?? '') }}">
                    </div>

                    <button class="btn btn-success">Simpan</button>
                    <a href="{{ route('pricing-rules.index') }}" class="btn btn-secondary">Kembali</a>

                </form>

            </div>
        </div>

    </div>
@endsection
