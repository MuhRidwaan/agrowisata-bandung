<div class="card-body">
    <p class="text-muted">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <button type="button" class="btn btn-danger mt-2" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i class="fas fa-trash-alt me-1"></i> {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true" @if($errors->userDeletion->isNotEmpty()) data-bs-show="true" @endif>
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body">
                        <p class="mb-3">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="form-group">
                            <label for="password" class="form-label">{{ __('Password') }} <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" placeholder="{{ __('Password') }}">
                            
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
    <!-- Script to auto-open modal if there are errors -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
            myModal.show();
        });
    </script>
@endif
