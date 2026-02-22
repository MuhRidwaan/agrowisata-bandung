@extends('backend.main_dashboard')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <h1>{{ isset($area) ? 'Edit Area' : 'Add Area' }}</h1>
    </div>
</section>

<section class="content">
<div class="container-fluid">

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card card-primary">

        <div class="card-header">
            <h3 class="card-title">Area Form</h3>
        </div>

        <form action="{{ isset($area) ? route('areas.update', $area->id) : route('areas.store') }}"
            method="POST">
            @csrf
            @if (isset($area)) @method('PUT') @endif

            <div class="card-body">

                <!-- AREA NAME -->
                <div class="form-group">
                    <label>Area Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $area->name ?? '') }}"
                        placeholder="Example: Lembang"
                        required>
                </div>

            </div>

            <div class="card-footer">
                <button class="btn btn-primary">
                    {{ isset($area) ? 'Update' : 'Save' }}
                </button>

                <a href="{{ route('areas.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

        </form>

    </div>

</div>
</section>
@endsection