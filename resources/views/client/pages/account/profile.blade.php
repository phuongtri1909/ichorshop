@extends('client.layouts.information')

@section('info_title', 'My Account - ' . request()->getHost())
@section('info_description', 'My Account - ' . request()->getHost())
@section('info_keyword', 'My Account, User Information, ' . request()->getHost())
@section('info_section_title', 'Account Details')

@section('info_content')
    <div class="account-details">
        <!-- Avatar Section -->
        <div class="avatar-section mb-4">
            <div class="d-flex align-items-center">
                <div class="profile-avatar-edit" id="avatar">
                    @if (!empty($user->avatar))
                        <img id="avatarImage" class="profile-avatar" src="{{ Storage::url($user->avatar) }}" alt="Avatar">
                    @else
                        <div class="profile-avatar d-flex align-items-center justify-content-center bg-light" id="defaultAvatar">
                            <span class="avatar-initials">{{ strtoupper(substr($user->full_name, 0, 2)) }}</span>
                        </div>
                    @endif
                    <div class="avatar-edit-overlay">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <div class="ms-3">
                    <h5 class="mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
            </div>
            <input type="file" id="avatarInput" style="display: none;" accept="image/*">
        </div>

        <!-- Account Information Form -->
        <form id="accountForm" class="account-form">
            @csrf
            <div class="form-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="full_name" class="form-label">Full name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="{{ $user->full_name }}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ $user->phone }}" placeholder="Enter phone number">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="{{ $user->email }}" readonly>
                    <small class="text-muted">Email cannot be changed</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-pry" id="saveChangesBtn">
                        <span class="btn-text">Save Changes</span>
                        <span class="btn-spinner d-none">
                            <i class="fas fa-spinner fa-spin me-2"></i>Saving...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Password Change Section -->
        <div class="password-section mt-5">
            <h6 class="section-title">Change Password</h6>
            <button type="button" class="btn btn-outline-secondary" id="changePasswordBtn">
                <i class="fas fa-key me-2"></i>Change Password
            </button>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="passwordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                <button type="button" class="password-toggle" data-target="current_password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="new_password" name="password" required>
                                <button type="button" class="password-toggle" data-target="new_password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" id="confirm_password" name="password_confirmation" required>
                                <button type="button" class="password-toggle" data-target="confirm_password">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-pry" id="updatePasswordBtn">
                        <span class="btn-text">Update Password</span>
                        <span class="btn-spinner d-none">
                            <i class="fas fa-spinner fa-spin me-2"></i>Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
          

            // Toggle loading state
            function toggleLoading(btn, loading) {
                const text = btn.find('.btn-text');
                const spinner = btn.find('.btn-spinner');
                btn.prop('disabled', loading);
                
                if (loading) {
                    text.addClass('d-none');
                    spinner.removeClass('d-none');
                } else {
                    text.removeClass('d-none');
                    spinner.addClass('d-none');
                }
            }

            // Clear form errors
            function clearErrors() {
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('').hide();
            }

            // Show field error
            function showFieldError(fieldName, message) {
                const field = $(`[name="${fieldName}"]`);
                
                // Find the invalid-feedback element - check multiple locations
                let feedback = field.siblings('.invalid-feedback');
                
                // If not found as sibling, look in the parent container
                if (feedback.length === 0) {
                    feedback = field.closest('.mb-3').find('.invalid-feedback');
                }
                
                // If still not found, look in the parent div
                if (feedback.length === 0) {
                    feedback = field.parent().siblings('.invalid-feedback');
                }
                
                // If still not found, look in the parent's parent
                if (feedback.length === 0) {
                    feedback = field.parent().parent().find('.invalid-feedback');
                }
                
                field.addClass('is-invalid');
                
                if (feedback.length > 0) {
                    feedback.text(message).show();
                } else {
                    // If still no feedback element found, create one
                    field.after(`<div class="invalid-feedback" style="display: block;">${message}</div>`);
                }
            }

            // Avatar upload
            $('#avatar').on('click', function() {
                $('#avatarInput').click();
            });

            $('#avatarInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        if (!$('#avatarImage').length) {
                            $('#defaultAvatar').replaceWith(`
                                <img id="avatarImage" class="profile-avatar" src="${e.target.result}" alt="Avatar">
                            `);
                        } else {
                            $('#avatarImage').attr('src', e.target.result);
                        }
                    };
                    reader.readAsDataURL(file);

                    // Upload avatar
                    const formData = new FormData();
                    formData.append('avatar', file);

                    $.ajax({
                        url: "{{ route('user.update.avatar') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast(response.message, 'success');
                            } else {
                                showToast(response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            if (response && response.message) {
                                Object.keys(response.message).forEach(field => {
                                    showToast(response.message[field][0], 'error');
                                });
                            } else {
                                showToast('Error uploading avatar', 'error');
                            }
                        }
                    });
                }
            });

            // Account form submission
            $('#accountForm').on('submit', function(e) {
                e.preventDefault();
                clearErrors();
                
                const saveBtn = $('#saveChangesBtn');
                toggleLoading(saveBtn, true);

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('user.update.profile') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            Object.keys(response.message).forEach(field => {
                                showFieldError(field, response.message[field][0]);
                            });
                        } else {
                            showToast('Error updating profile', 'error');
                        }
                    },
                    complete: function() {
                        toggleLoading(saveBtn, false);
                    }
                });
            });

            // Password toggle functionality
            $('.password-toggle').on('click', function() {
                const target = $(this).data('target');
                const input = $(`#${target}`);
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Show password modal
            $('#changePasswordBtn').on('click', function() {
                $('#passwordModal').modal('show');
            });

            // Password form submission
            $('#updatePasswordBtn').on('click', function() {
                clearErrors();
                
                const updateBtn = $(this);
                const form = $('#passwordForm');
                const formData = form.serialize();

                // Client-side validation
                const newPassword = $('#new_password').val();
                const confirmPassword = $('#confirm_password').val();
                
                if (newPassword !== confirmPassword) {
                    showFieldError('password_confirmation', 'Passwords do not match');
                    return;
                }

                if (newPassword.length < 6) {
                    showFieldError('password', 'Password must be at least 6 characters');
                    return;
                }

                toggleLoading(updateBtn, true);

                $.ajax({
                    url: "{{ route('user.update.password') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            $('#passwordModal').modal('hide');
                            form[0].reset();
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        console.log('Password update error:', response);
                        
                        if (response && response.message) {
                            Object.keys(response.message).forEach(field => {
                                showFieldError(field, response.message[field][0]);
                            });
                        } else {
                            showToast('Error updating password', 'error');
                        }
                    },
                    complete: function() {
                        toggleLoading(updateBtn, false);
                    }
                });
            });

            // Reset modal when closed
            $('#passwordModal').on('hidden.bs.modal', function() {
                $('#passwordForm')[0].reset();
                clearErrors();
            });
        });
    </script>
@endpush
