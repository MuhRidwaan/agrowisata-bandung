<div class="card-body">
    <p class="text-muted">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group mb-3">
            <label for="update_password_current_password">{{ __('Current Password') }} <span class="text-danger">*</span></label>
            <input type="password" id="update_password_current_password" name="current_password" class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password">
            @if($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback">
                    {{ $errors->updatePassword->first('current_password') }}
                </div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label for="update_password_password">{{ __('New Password') }} <span class="text-danger">*</span></label>
            <input type="password" id="update_password_password" name="password" class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password">
            @if($errors->updatePassword->has('password'))
                <div class="invalid-feedback">
                    {{ $errors->updatePassword->first('password') }}
                </div>
            @endif
        </div>

        <div class="form-group mb-4">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
            <input type="password" id="update_password_password_confirmation" name="password_confirmation" class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" autocomplete="new-password">
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center mb-0">
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-key me-1"></i> {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success ms-3" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    <i class="fas fa-check-circle"></i> {{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>
</div>
