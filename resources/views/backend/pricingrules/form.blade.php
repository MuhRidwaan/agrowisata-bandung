@push('scripts')
<script>
let ruleIndex = {{ isset($rules) ? count($rules) : (isset($rule) ? 1 : 0) }};
function addRuleRow() {
    const wrapper = document.getElementById('rules-wrapper');
    const row = document.createElement('tr');
    row.className = 'rule-row';
    row.innerHTML = `
        <td><input type="number" name="rules[
        ${ruleIndex}
        ][min_pax]" class="form-control" min="1" required></td>
        <td><input type="number" name="rules[
        ${ruleIndex}
        ][max_pax]" class="form-control" min="1" required></td>
        <td>
            <select name="rules[
        ${ruleIndex}
        ][discount_type]" class="form-control" required>
                <option value="">-- Select Type --</option>
                <option value="percent">Percent</option>
                <option value="nominal">Nominal</option>
            </select>
        </td>
        <td><input type="number" name="rules[
        ${ruleIndex}
        ][discount_value]" class="form-control" min="0" required></td>
        <td><input type="text" name="rules[
        ${ruleIndex}
        ][description]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRuleRow(this)">-</button></td>
    `;
    wrapper.appendChild(row);
    ruleIndex++;
}
function removeRuleRow(btn) {
    const row = btn.closest('tr');
    row.parentNode.removeChild(row);
}
</script>
@endpush
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



                    {{-- Dynamic Rules Table --}}
                    <div class="mb-3">
                        <label>Pricing Rules</label>
                        <table class="table table-bordered" id="rules-table">
                            <thead>
                                <tr>
                                    <th>Minimum Pax</th>
                                    <th>Maximum Pax</th>
                                    <th>Discount Type</th>
                                    <th>Discount Value</th>
                                    <th>Description</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="rules-wrapper">
                                @php $rules = old('rules', isset($rules) ? $rules : [isset($rule) ? $rule->toArray() : []]); @endphp
                                @foreach($rules as $i => $row)
                                <tr class="rule-row">
                                    <td>
                                        <input type="number" name="rules[{{ $i }}][min_pax]" class="form-control" min="1" value="{{ $row['min_pax'] ?? '' }}" required>
                                        @error('rules.'.$i.'.min_pax')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td><input type="number" name="rules[{{ $i }}][max_pax]" class="form-control" min="1" value="{{ $row['max_pax'] ?? '' }}" required></td>
                                    <td>
                                        <select name="rules[{{ $i }}][discount_type]" class="form-control" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="percent" {{ ($row['discount_type'] ?? '') == 'percent' ? 'selected' : '' }}>Percent</option>
                                            <option value="nominal" {{ ($row['discount_type'] ?? '') == 'nominal' ? 'selected' : '' }}>Nominal</option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="rules[{{ $i }}][discount_value]" class="form-control" min="0" value="{{ $row['discount_value'] ?? '' }}" required></td>
                                    <td><input type="text" name="rules[{{ $i }}][description]" class="form-control" value="{{ $row['description'] ?? '' }}"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRuleRow(this)">-</button></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-info btn-sm" onclick="addRuleRow()">+ Add Rule</button>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('pricingrules.index') }}" class="btn btn-secondary">Back</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</section>

@endsection