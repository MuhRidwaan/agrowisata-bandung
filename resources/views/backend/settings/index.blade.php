@extends('backend.main_dashboard')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Global Settings</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Global Settings</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            @foreach($settings as $category => $items)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }} text-capitalize" 
                                   id="tab-{{ $category }}" data-toggle="pill" href="#content-{{ $category }}" 
                                   role="tab" aria-controls="content-{{ $category }}" aria-selected="true">
                                   {{ $category }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                @foreach($settings as $category => $items)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                     id="content-{{ $category }}" role="tabpanel" aria-labelledby="tab-{{ $category }}">
                                    
                                    @foreach($items as $setting)
                                    <div class="form-group row mb-4">
                                        <label class="col-sm-3 col-form-label">{{ $setting->label }}</label>
                                        <div class="col-sm-9">
                                            @if($setting->type == 'text')
                                                <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                            @elseif($setting->type == 'number')
                                                <input type="number" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                                            @elseif($setting->type == 'textarea')
                                                <textarea name="{{ $setting->key }}" class="form-control" rows="3">{{ $setting->value }}</textarea>
                                            @elseif($setting->type == 'checkbox')
                                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                                    <input type="checkbox" name="{{ $setting->key }}" class="custom-control-input" 
                                                           id="switch-{{ $setting->key }}" {{ $setting->value == 'true' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="switch-{{ $setting->key }}">Enable / Disable</label>
                                                </div>
                                            @elseif($setting->type == 'file')
                                                @if($setting->value)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $setting->value) }}" alt="Preview" style="max-height: 50px;">
                                                    </div>
                                                @endif
                                                <div class="custom-file">
                                                    <input type="file" name="{{ $setting->key }}" class="custom-file-input">
                                                    <label class="custom-file-label">Choose file</label>
                                                </div>
                                            @endif
                                            <small class="text-muted">Key: <code>{{ $setting->key }}</code></small>
                                        </div>
                                    </div>
                                    @endforeach

                                </div>
                                @endforeach
                            </div>
                            
                            <div class="card-footer bg-white border-top">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save mr-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });
</script>
@endpush
