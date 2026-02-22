@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>
                    {{ isset($tier) ? 'Edit Pricing Tier' : 'Add Pricing Tier' }}
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pricingtiers.index') }}">
                            Pricing Tier Data
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($tier) ? 'Edit' : 'Add' }} Pricing Tier
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
                      action="{{ isset($tier)
                          ? route('pricingtiers.update', $tier->id)
                          : route('pricingtiers.store') }}">

                    @csrf

                    @if (isset($tier))
                        @method('PUT')
                    @endif

                    {{-- Tour Package --}}
                    <div class="mb-3">
                        <label>Tour Package</label>

                        <select name="tour_package_id"
                                class="form-control"
                                required>
                            <option value="">-- Select Package --</option>

                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('tour_package_id', $tier->tour_package_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Name --}}
                    <div class="mb-3">
                        <label>Category Name</label>

                        <select name="name"
                                class="form-control"
                                required>
                            <option value="">-- Select Category --</option>

                            @foreach (['Baby', 'Children', 'Adult'] as $category)
                                <option value="{{ $category }}"
                                    {{ old('name', $tier->name ?? '') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Price --}}
                    <div class="mb-3">
                        <label>Price</label>

                        <input type="number"
                               name="price"
                               class="form-control"
                               value="{{ old('price', $tier->price ?? '') }}"
                               required>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-3">
                        <button type="submit"
                                class="btn btn-primary">
                            Save
                        </button>

                        <a href="{{ route('pricingtiers.index') }}"
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