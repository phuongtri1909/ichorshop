@extends('client.layouts.app')
@section('title', 'Register')
@section('description', 'Create a new account to access exclusive features and content.')
@section('keywords', 'register, user authentication, ecommerce register')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles_auth.css') }}">
    <style>
        .otp-container {
            display: none;
        }
        
        .otp-container.show {
            display: block;
        }
        
        .registration-form.hide {
            display: none;
        }
        
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }
        
        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            background: #f9f9f9;
            transition: all 0.3s ease;
        }
        
        .otp-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .otp-input.filled {
            background: var(--primary-color-2);
            border-color: var(--primary-color);
        }
        
        .otp-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .otp-info h4 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .otp-info p {
            color: var(--primary-color-5);
            margin: 0;
        }
        
        .resend-otp {
            text-align: center;
            margin-top: 15px;
        }
        
        .resend-otp button {
            background: none;
            border: none;
            color: var(--primary-color);
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
        }
        
        .resend-otp button:disabled {
            color: var(--primary-color-5);
            cursor: not-allowed;
            text-decoration: none;
        }
        
        .back-to-form {
            text-align: center;
            margin-top: 15px;
        }
        
        .back-to-form button {
            background: none;
            border: none;
            color: var(--primary-color-5);
            text-decoration: underline;
            cursor: pointer;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .otp-inputs {
                gap: 8px;
            }
            
            .otp-input {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
@endpush

@section('content')
    <x-breadcrumb :items="[['title' => 'Home', 'url' => route('home')], ['title' => 'Register', 'url' => '']]" title="Register" />

    <div class="auth-container d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card p-4 p-md-5">
                        <!-- Registration Form -->
                        <div class="registration-form">
                            <form id="registerForm">
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
                                    <label for="full_name" class="form-label color-primary-3">Full name</label>
                                    <input type="text" class="form-control" name="full_name" id="full_name" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label color-primary-3">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="mb-3 position-relative">
                                    <label for="password" class="form-label color-primary-3">Password</label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fa fa-eye" id="toggleIcon"></i>
                                    </button>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <p class="text-muted small mb-3">By creating an account you agree with our Terms of Service, Privacy Policy.</p>

                                <button type="submit" class="auth-btn btn w-100" id="createAccountBtn">
                                    <span class="btn-text">Create account</span>
                                    <span class="btn-spinner d-none">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Creating...
                                    </span>
                                </button>

                                <div class="text-center signup-text">
                                    <span>Already have an account? </span>
                                    <a href="{{ route('login') }}" class="auth-link color-primary-3">Log in</a>
                                </div>
                            </form>
                        </div>

                        <!-- OTP Verification -->
                        <div class="otp-container">
                            <div class="otp-info">
                                <h4>Verify Your Email</h4>
                                <p>We've sent a 6-digit code to <strong id="sentToEmail"></strong></p>
                                <p class="small text-muted mt-2">Please enter the code to continue</p>
                            </div>

                            <form id="otpForm">
                                @csrf
                                <input type="hidden" name="full_name" id="hiddenFullName">
                                <input type="hidden" name="email" id="hiddenEmail">
                                <input type="hidden" name="password" id="hiddenPassword">
                                
                                <div class="otp-inputs">
                                    <input type="text" class="otp-input" maxlength="1" data-index="0">
                                    <input type="text" class="otp-input" maxlength="1" data-index="1">
                                    <input type="text" class="otp-input" maxlength="1" data-index="2">
                                    <input type="text" class="otp-input" maxlength="1" data-index="3">
                                    <input type="text" class="otp-input" maxlength="1" data-index="4">
                                    <input type="text" class="otp-input" maxlength="1" data-index="5">
                                </div>
                                
                                <div class="invalid-feedback text-center mb-3" id="otpError"></div>

                                <button type="submit" class="auth-btn btn w-100" id="verifyOtpBtn">
                                    <span class="btn-text">Verify & Create Account</span>
                                    <span class="btn-spinner d-none">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Verifying...
                                    </span>
                                </button>

                                <div class="resend-otp">
                                    <p class="small text-muted">Didn't receive the code?</p>
                                    <button type="button" id="resendOtpBtn">Resend OTP</button>
                                    <span class="resend-timer d-none">Resend in <span id="timer">60</span>s</span>
                                </div>

                                <div class="back-to-form">
                                    <button type="button" id="backToFormBtn">← Back to registration</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput && toggleIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    toggleIcon.classList.toggle('fa-eye');
                    toggleIcon.classList.toggle('fa-eye-slash');
                });
            }

            // Form elements
            const registerForm = document.getElementById('registerForm');
            const otpForm = document.getElementById('otpForm');
            const registrationFormDiv = document.querySelector('.registration-form');
            const otpContainer = document.querySelector('.otp-container');
            const otpInputs = document.querySelectorAll('.otp-input');
            const createAccountBtn = document.getElementById('createAccountBtn');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const resendOtpBtn = document.getElementById('resendOtpBtn');
            const backToFormBtn = document.getElementById('backToFormBtn');

            // Clear form errors
            function clearErrors() {
                document.querySelectorAll('.form-control').forEach(input => {
                    input.classList.remove('is-invalid');
                });
                document.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                });
            }

            // Show field error
            function showFieldError(fieldName, message) {
                const field = document.querySelector(`[name="${fieldName}"]`);
                const feedback = field.nextElementSibling;
                field.classList.add('is-invalid');
                feedback.textContent = message;
                feedback.style.display = 'block';
            }

            // Toggle loading state
            function toggleLoading(btn, loading) {
                const text = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.btn-spinner');
                btn.disabled = loading;
                
                if (loading) {
                    text.classList.add('d-none');
                    spinner.classList.remove('d-none');
                } else {
                    text.classList.remove('d-none');
                    spinner.classList.add('d-none');
                }
            }

            // Registration form submit
            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors();
                toggleLoading(createAccountBtn, true);

                const formData = new FormData(this);

                try {
                    const response = await fetch('{{ route("register") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'otp_sent') {
                        // Show OTP form
                        showOtpForm(data.data);
                        showToast(data.message, 'success');
                    } else if (data.status === 'error') {
                        // Show validation errors
                        if (data.message) {
                            Object.keys(data.message).forEach(field => {
                                showFieldError(field, data.message[field][0]);
                            });
                            
                            // Show toast for email error specifically
                            if (data.message.email) {
                                showToast(data.message.email[0], 'error');
                            }
                        }
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.', 'error');
                } finally {
                    toggleLoading(createAccountBtn, false);
                }
            });

            // Show OTP form
            function showOtpForm(data) {
                registrationFormDiv.classList.add('hide');
                otpContainer.classList.add('show');
                
                // Fill hidden fields
                document.getElementById('hiddenFullName').value = data.full_name;
                document.getElementById('hiddenEmail').value = data.email;
                document.getElementById('hiddenPassword').value = data.password;
                document.getElementById('sentToEmail').textContent = data.email;
                
                // Focus first OTP input
                otpInputs[0].focus();
                
                // Start resend timer
                startResendTimer();
            }

            // Hide OTP form
            function hideOtpForm() {
                registrationFormDiv.classList.remove('hide');
                otpContainer.classList.remove('show');
                clearOtpInputs();
            }

            // OTP input handling
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    // Only allow numbers
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    if (value) {
                        e.target.classList.add('filled');
                        
                        // Move to next input
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }
                    
                    // Clear OTP error when user starts typing
                    document.getElementById('otpError').style.display = 'none';
                });

                input.addEventListener('keydown', function(e) {
                    // Handle backspace
                    if (e.key === 'Backspace') {
                        if (!e.target.value && index > 0) {
                            // Move to previous input and clear it
                            otpInputs[index - 1].focus();
                            otpInputs[index - 1].value = '';
                            otpInputs[index - 1].classList.remove('filled');
                        } else if (e.target.value) {
                            // Clear current input
                            e.target.value = '';
                            e.target.classList.remove('filled');
                        }
                    }
                    
                    // Handle arrow keys
                    if (e.key === 'ArrowLeft' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

                // Handle paste
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = paste.replace(/\D/g, '').slice(0, 6);
                    
                    numbers.split('').forEach((num, idx) => {
                        if (otpInputs[idx]) {
                            otpInputs[idx].value = num;
                            otpInputs[idx].classList.add('filled');
                        }
                    });
                    
                    // Focus the next empty input or last input
                    const nextEmpty = Math.min(numbers.length, otpInputs.length - 1);
                    otpInputs[nextEmpty].focus();
                });
            });

            // Clear OTP inputs
            function clearOtpInputs() {
                otpInputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('filled');
                });
            }

            // Get OTP value
            function getOtpValue() {
                return Array.from(otpInputs).map(input => input.value).join('');
            }

            // OTP form submit
            otpForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const otp = getOtpValue();
                if (otp.length !== 6) {
                    document.getElementById('otpError').textContent = 'Please enter all 6 digits';
                    document.getElementById('otpError').style.display = 'block';
                    return;
                }

                toggleLoading(verifyOtpBtn, true);

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('full_name', document.getElementById('hiddenFullName').value);
                formData.append('email', document.getElementById('hiddenEmail').value);
                formData.append('password', document.getElementById('hiddenPassword').value);
                formData.append('otp', otp);

                try {
                    const response = await fetch('{{ route("register") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        window.location.href = data.url;
                    } else if (data.status === 'error') {
                        if (data.message.otp) {
                            document.getElementById('otpError').textContent = data.message.otp[0];
                            document.getElementById('otpError').style.display = 'block';
                        } else {
                            showToast(data.message, 'error');
                        }
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.', 'error');
                } finally {
                    toggleLoading(verifyOtpBtn, false);
                }
            });

            // Resend OTP
            let resendTimer;
            function startResendTimer() {
                let timeLeft = 60;
                resendOtpBtn.style.display = 'none';
                document.querySelector('.resend-timer').classList.remove('d-none');
                
                resendTimer = setInterval(() => {
                    timeLeft--;
                    document.getElementById('timer').textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(resendTimer);
                        resendOtpBtn.style.display = 'inline';
                        document.querySelector('.resend-timer').classList.add('d-none');
                    }
                }, 1000);
            }

            resendOtpBtn.addEventListener('click', async function() {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('full_name', document.getElementById('hiddenFullName').value);
                formData.append('email', document.getElementById('hiddenEmail').value);
                formData.append('password', document.getElementById('hiddenPassword').value);

                try {
                    const response = await fetch('{{ route("register") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    if (data.status === 'otp_sent') {
                        showToast(data.message, 'success');
                        clearOtpInputs();
                        otpInputs[0].focus();
                        startResendTimer();
                    }
                } catch (error) {
                    showToast('Failed to resend OTP. Please try again.', 'error');
                }
            });

            // Back to form
            backToFormBtn.addEventListener('click', function() {
                hideOtpForm();
                if (resendTimer) {
                    clearInterval(resendTimer);
                }
            });
        });
    </script>
@endpush
