@extends('client.layouts.app')
@section('title', 'Login')
@section('description', 'Login to your account to access exclusive features and content.')
@section('keywords', 'login, user authentication, ecommerce login')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles_auth.css') }}">
@endpush

@section('content')
    <x-breadcrumb :items="[['title' => 'Home', 'url' => route('home')], ['title' => 'Login', 'url' => '']]" title="Login" />

    <div class="auth-container d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card p-4 p-md-5">
                        <form action="{{ route('login.post') }}" method="post">
                            @csrf

                            <div class="text-center mb-4">
                                <a href="{{ route('login.google') }}"
                                    class="google-btn btn w-100 d-flex align-items-center justify-content-center color-primary-3">
                                    <img src="{{ asset('assets/images/svg/Google.svg') }}" alt="Google" class="me-2"
                                        height="18">
                                    Continue with Google
                                </a>

                                <div class="divider">
                                    <p>OR</p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label color-primary-3">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" id="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="password" class="form-label color-primary-3">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    name="password" id="password" required>
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fa fa-eye" id="toggleIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="forgot-password">
                                <a href="{{ route('forgot-password') }}" class="auth-link color-primary-3">Forgot
                                    Password?</a>
                            </div>

                            <button type="submit" class="auth-btn btn w-100">Login</button>

                            <div class="text-center signup-text">
                                <span>Don't have an account? </span>
                                <a href="{{ route('register') }}" class="auth-link color-primary-3">Sign up</a>
                            </div>
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
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
@endpush
