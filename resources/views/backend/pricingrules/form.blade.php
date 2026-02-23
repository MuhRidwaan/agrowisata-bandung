@extends('backend.main_dashboard')

@section('content')
    <div class="container-fluid">

        <h4 class="mb-3">{{ isset($rule) ? 'Edit Rule' : 'Add Discount Rule' }}</h4>

        <div class="card">
            <div class="card-body">

                <form method="POST"
                    action="{{ isset($rule) ? route('pricingrules.update', $rule->id) : route('pricingrules.store') }}">
                    @csrf
                    @if (isset($rule))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label>Package</label>
                        <select name="tour_package_id" class="form-control" required>
                            <option value="">-- select --</option>
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
                            <label>Minimum Pax</label>
                            <input type="number" name="min_pax" class="form-control"
                                value="{{ old('min_pax', $rule->min_pax ?? '') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Maximum Pax</label>
                            <input type="number" name="max_pax" class="form-control"
                                value="{{ old('max_pax', $rule->max_pax ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Discount Type</label>
                        <select name="discount_type" class="form-control">
                            <option value="percent"
                                {{ old('discount_type', $rule->discount_type ?? '') == 'percent' ? 'selected' : '' }}>Percent</option>
                            <option value="nominal"
                                {{ old('discount_type', $rule->discount_type ?? '') == 'nominal' ? 'selected' : '' }}>Nominal
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Discount Value</label>
                        <input type="number" name="discount_value" class="form-control"
                            value="{{ old('discount_value', $rule->discount_value ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control"
                            value="{{ old('description', $rule->description ?? '') }}">
                    </div>

                    <button class="btn btn-success">Save</button>
                    <a href="{{ route('pricingrules.index') }}" class="btn btn-secondary">Back</a>

                </form>

            </div>
        </div>

    </div>
@endsection