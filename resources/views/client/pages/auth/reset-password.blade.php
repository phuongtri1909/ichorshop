@extends('client.layouts.app')
@section('title', 'Reset Password')
@section('description', 'Reset your password to regain access to your account.')
@section('keywords', 'reset password, user authentication, ecommerce reset password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles_auth.css') }}">
@endpush

@section('content')
    <x-breadcrumb :items="[['title' => 'Home', 'url' => route('home')], ['title' => 'Reset Password', 'url' => '']]" title="Reset Password" />

    <div class="auth-container d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card p-4 p-md-5">
                        <form action="{{ route('reset.password') }}" method="post">
                            @csrf

                            <div class="mb-3 position-relative">
                                <label for="new_password" class="form-label color-primary-3">New Password</label>
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                    name="new_password" id="new_password" required>
                                <button type="button" class="password-toggle" id="toggleNewPassword">
                                    <i class="fa fa-eye" id="toggleNewPasswordIcon"></i>
                                </button>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="confirm_password" class="form-label color-primary-3">Confirm Password</label>
                                <input type="password" class="form-control @error('confirm_password') is-invalid @enderror"
                                    name="confirm_password" id="confirm_password" required>
                                <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                    <i class="fa fa-eye" id="toggleConfirmPasswordIcon"></i>
                                </button>
                                @error('confirm_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="auth-btn btn w-100">Reset password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle cho New Password
            const toggleNewPassword = document.getElementById('toggleNewPassword');
            const newPasswordInput = document.getElementById('new_password');
            const toggleNewPasswordIcon = document.getElementById('toggleNewPasswordIcon');

            if (toggleNewPassword && newPasswordInput && toggleNewPasswordIcon) {
                toggleNewPassword.addEventListener('click', function() {
                    const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    newPasswordInput.setAttribute('type', type);

                    if (type === 'text') {
                        toggleNewPasswordIcon.classList.remove('fa-eye');
                        toggleNewPasswordIcon.classList.add('fa-eye-slash');
                    } else {
                        toggleNewPasswordIcon.classList.remove('fa-eye-slash');
                        toggleNewPasswordIcon.classList.add('fa-eye');
                    }
                });
            }

            // Toggle cho Confirm Password
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const toggleConfirmPasswordIcon = document.getElementById('toggleConfirmPasswordIcon');

            if (toggleConfirmPassword && confirmPasswordInput && toggleConfirmPasswordIcon) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);

                    if (type === 'text') {
                        toggleConfirmPasswordIcon.classList.remove('fa-eye');
                        toggleConfirmPasswordIcon.classList.add('fa-eye-slash');
                    } else {
                        toggleConfirmPasswordIcon.classList.remove('fa-eye-slash');
                        toggleConfirmPasswordIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
@endpush
