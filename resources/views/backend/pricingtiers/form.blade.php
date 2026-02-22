@extends('backend.main_dashboard')

@section('content')

<section class="content-header">
    <div class="container-fluid">

        <div class="row mb-2">

            <div class="col-sm-6">
                <h1>
                    {{ isset($tier) ? 'Edit Pricing Tier' : 'Tambah Pricing Tier' }}
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('pricingtiers.index') }}">
                            Data Pricing Tier
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ isset($tier) ? 'Edit' : 'Tambah' }} Pricing Tier
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

                    <div class="mb-3">
                        <label>Paket Tour</label>
                        <select name="tour_package_id"
                                class="form-control"
                                required>
                            <option value="">-- pilih paket --</option>

                            @foreach ($packages as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('tour_package_id', $tier->tour_package_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Nama Kategori</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $tier->name ?? '') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label>Harga</label>
                        <input type="number"
                               name="price"
                               class="form-control"
                               value="{{ old('price', $tier->price ?? '') }}"
                               required>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="{{ route('pricingtiers.index') }}"
                           class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</section>

@endsection