@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>
                    {{ isset($rule) ? 'Edit Pricing Rule' : 'Add Pricing Rule' }}
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pricingrules.index') }}">
                            Pricing Rule Data
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($rule) ? 'Edit' : 'Add' }} Pricing Rule
                    </li>
                </ol>
            </div>

        </div>
    </div>
</section>


<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-body">

                <form method="POST"
                      action="{{ isset($rule)
                          ? route('pricingrules.update', $rule->id)
                          : route('pricingrules.store') }}">

                    @csrf
                    @if (isset($rule))
                        @method('PUT')
                    @endif


                    {{-- Package --}}
                    <div class="mb-3">
                        <label>
                            Package <span class="text-danger">*</span>
                        </label>

                        <select name="paket_tour_id"
                                class="form-control"
                                required>
                            <option value="">-- Select Package --</option>

                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('paket_tour_id', $rule->paket_tour_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>

                        @error('paket_tour_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- Pax Range --}}
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>
                                Minimum Pax <span class="text-danger">*</span>
                            </label>

                            <input type="number"
                                   name="min_pax"
                                   class="form-control"
                                   min="1"
                                   value="{{ old('min_pax', $rule->min_pax ?? '') }}"
                                   required>

                            @error('min_pax')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>
                                Maximum Pax <span class="text-danger">*</span>
                            </label>

                            <input type="number"
                                   name="max_pax"
                                   class="form-control"
                                   min="1"
                                   value="{{ old('max_pax', $rule->max_pax ?? '') }}"
                                   required>

                            @error('max_pax')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>


                    {{-- Discount Type --}}
                    <div class="mb-3">
                        <label>
                            Discount Type <span class="text-danger">*</span>
                        </label>

                        <select name="discount_type"
                                class="form-control"
                                required>
                            <option value="">-- Select Type --</option>

                            <option value="percent"
                                {{ old('discount_type', $rule->discount_type ?? '') == 'percent' ? 'selected' : '' }}>
                                Percent
                            </option>

                            <option value="nominal"
                                {{ old('discount_type', $rule->discount_type ?? '') == 'nominal' ? 'selected' : '' }}>
                                Nominal
                            </option>
                        </select>

                        @error('discount_type')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- Discount Value --}}
                    <div class="mb-3">
                        <label>
                            Discount Value <span class="text-danger">*</span>
                        </label>

                        <input type="number"
                               name="discount_value"
                               class="form-control"
                               min="0"
                               value="{{ old('discount_value', $rule->discount_value ?? '') }}"
                               required>

                        @error('discount_value')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- Description --}}
                    <div class="mb-3">
                        <label>Description</label>

                        <input type="text"
                               name="description"
                               class="form-control"
                               value="{{ old('description', $rule->description ?? '') }}">
                    </div>


                    {{-- Buttons --}}
                    <div class="mt-3">
                        <button type="submit"
                                class="btn btn-primary">
                            Save
                        </button>

                        <a href="{{ route('pricingrules.index') }}"
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