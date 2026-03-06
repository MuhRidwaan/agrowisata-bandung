<div class="card-body">
    <p class="text-muted">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="form-group mb-3">
            <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-warning">
                    <p class="mb-1 text-sm">
                        <i class="fas fa-exclamation-triangle"></i> {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline ms-1">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="text-success text-sm fw-bold">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center mb-0">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success ms-3" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)">
                    <i class="fas fa-check-circle"></i> {{ __('Saved.') }}
                </span>
            @endif
        </div>
    </form>
</div>
