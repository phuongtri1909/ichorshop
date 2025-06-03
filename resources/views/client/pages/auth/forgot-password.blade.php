@extends('client.layouts.app')
@section('title', 'Forgot Password')
@section('description', 'Reset your password to regain access to your account.')
@section('keywords', 'forgot password, user authentication, ecommerce forgot password')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/styles_auth.css') }}">
@endpush

@section('content')
    <x-breadcrumb :items="[['title' => 'Home', 'url' => route('home')], ['title' => 'Forgot Password', 'url' => '']]" title="Forgot Password" />

    <div class="auth-container d-flex align-items-center justify-content-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                    <div class="auth-card p-4 p-md-5">
                        <!-- Email Form -->
                        <div class="email-form">
                            <form id="emailForm">
                                @csrf
                                <div class="text-center mb-4">
                                    <h3 class="auth-title">Forgot Password</h3>
                                    <p class="text-muted">Please enter the email address associated with your account. We'll promptly send you an OTP to reset your password.</p>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label color-primary-3">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" required>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <button type="submit" class="auth-btn btn w-100" id="sendOtpBtn">
                                    <span class="btn-text">Send OTP</span>
                                    <span class="btn-spinner d-none">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Sending...
                                    </span>
                                </button>

                                <div class="text-center mt-3">
                                    <a href="{{ route('login') }}" class="auth-link color-primary-3">Back to Login</a>
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
                                <input type="hidden" name="email" id="hiddenEmail">
                                
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
                                    <span class="btn-text">Verify OTP</span>
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
                                    <button type="button" id="backToEmailBtn">← Back to email</button>
                                </div>
                            </form>
                        </div>

                        <!-- New Password Form -->
                        <div class="password-container">
                            <div class="password-info">
                                <h4>Reset Your Password</h4>
                                <p>Please enter your new password</p>
                            </div>

                            <form id="passwordForm">
                                @csrf
                                <input type="hidden" name="email" id="hiddenEmailPassword">
                                <input type="hidden" name="otp" id="hiddenOtp">

                                <div class="mb-3 position-relative">
                                    <label for="password" class="form-label color-primary-3">New Password</label>
                                    <input type="password" class="form-control" name="password" id="password" required>
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <i class="fa fa-eye" id="toggleIcon"></i>
                                    </button>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="mb-3 position-relative">
                                    <label for="password_confirmation" class="form-label color-primary-3">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                    <button type="button" class="password-toggle" id="togglePasswordConfirm">
                                        <i class="fa fa-eye" id="toggleIconConfirm"></i>
                                    </button>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <button type="submit" class="auth-btn btn w-100" id="resetPasswordBtn">
                                    <span class="btn-text">Reset Password</span>
                                    <span class="btn-spinner d-none">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Resetting...
                                    </span>
                                </button>

                                <div class="back-to-form">
                                    <button type="button" id="backToOtpBtn">← Back to OTP</button>
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
            // Form elements
            const emailForm = document.getElementById('emailForm');
            const otpForm = document.getElementById('otpForm');
            const passwordForm = document.getElementById('passwordForm');
            const emailFormDiv = document.querySelector('.email-form');
            const otpContainer = document.querySelector('.otp-container');
            const passwordContainer = document.querySelector('.password-container');
            const otpInputs = document.querySelectorAll('.otp-input');

            // Buttons
            const sendOtpBtn = document.getElementById('sendOtpBtn');
            const verifyOtpBtn = document.getElementById('verifyOtpBtn');
            const resetPasswordBtn = document.getElementById('resetPasswordBtn');
            const resendOtpBtn = document.getElementById('resendOtpBtn');
            const backToEmailBtn = document.getElementById('backToEmailBtn');
            const backToOtpBtn = document.getElementById('backToOtpBtn');

            // Password toggles
            setupPasswordToggle('togglePassword', 'password', 'toggleIcon');
            setupPasswordToggle('togglePasswordConfirm', 'password_confirmation', 'toggleIconConfirm');

            function setupPasswordToggle(toggleId, inputId, iconId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);

                if (toggle && input && icon) {
                    toggle.addEventListener('click', function() {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    });
                }
            }

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

            // Show forms
            function showEmailForm() {
                emailFormDiv.classList.remove('hide');
                otpContainer.classList.remove('show');
                passwordContainer.classList.remove('show');
                clearOtpInputs();
            }

            function showOtpForm(email) {
                emailFormDiv.classList.add('hide');
                otpContainer.classList.add('show');
                passwordContainer.classList.remove('show');
                
                document.getElementById('hiddenEmail').value = email;
                document.getElementById('sentToEmail').textContent = email;
                otpInputs[0].focus();
                startResendTimer();
            }

            function showPasswordForm(email, otp) {
                emailFormDiv.classList.add('hide');
                otpContainer.classList.remove('show');
                passwordContainer.classList.add('show');
                
                document.getElementById('hiddenEmailPassword').value = email;
                document.getElementById('hiddenOtp').value = otp;
                document.getElementById('password').focus();
            }

            // Email form submit
            emailForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors();
                toggleLoading(sendOtpBtn, true);

                const formData = new FormData(this);

                try {
                    const response = await fetch('{{ route("forgot.password") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        showOtpForm(document.getElementById('email').value);
                        showToast(data.message, 'success');
                    } else if (data.status === 'error') {
                        if (data.message) {
                            Object.keys(data.message).forEach(field => {
                                showFieldError(field, data.message[field][0]);
                            });
                            
                            if (data.message.email) {
                                showToast(data.message.email[0]);
                            }
                        }
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.');
                } finally {
                    toggleLoading(sendOtpBtn, false);
                }
            });

            // OTP input handling (same as register)
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    const value = e.target.value;
                    
                    if (!/^\d*$/.test(value)) {
                        e.target.value = '';
                        return;
                    }
                    
                    if (value) {
                        e.target.classList.add('filled');
                        if (index < otpInputs.length - 1) {
                            otpInputs[index + 1].focus();
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }
                    
                    document.getElementById('otpError').style.display = 'none';
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace') {
                        if (!e.target.value && index > 0) {
                            otpInputs[index - 1].focus();
                            otpInputs[index - 1].value = '';
                            otpInputs[index - 1].classList.remove('filled');
                        } else if (e.target.value) {
                            e.target.value = '';
                            e.target.classList.remove('filled');
                        }
                    }
                    
                    if (e.key === 'ArrowLeft' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                    if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                });

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
                formData.append('email', document.getElementById('hiddenEmail').value);
                formData.append('otp', otp);

                try {
                    const response = await fetch('{{ route("forgot.password") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        showPasswordForm(document.getElementById('hiddenEmail').value, otp);
                        showToast(data.message, 'success');
                    } else if (data.status === 'error') {
                        if (data.message.otp) {
                            document.getElementById('otpError').textContent = data.message.otp[0];
                            document.getElementById('otpError').style.display = 'block';
                        } else {
                            showToast(data.message);
                        }
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.');
                } finally {
                    toggleLoading(verifyOtpBtn, false);
                }
            });

            // Password form submit
            passwordForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearErrors();

                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirmation').value;

                if (password !== passwordConfirm) {
                    showFieldError('password_confirmation', 'Passwords do not match');
                    return;
                }

                toggleLoading(resetPasswordBtn, true);

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('email', document.getElementById('hiddenEmailPassword').value);
                formData.append('otp', document.getElementById('hiddenOtp').value);
                formData.append('password', password);

                try {
                    const response = await fetch('{{ route("forgot.password") }}', {
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
                        if (data.message) {
                            Object.keys(data.message).forEach(field => {
                                showFieldError(field, data.message[field][0]);
                            });
                        }
                    }
                } catch (error) {
                    showToast('An error occurred. Please try again.');
                } finally {
                    toggleLoading(resetPasswordBtn, false);
                }
            });

            // Resend OTP functionality
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
                formData.append('email', document.getElementById('hiddenEmail').value);

                try {
                    const response = await fetch('{{ route("forgot.password") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    if (data.status === 'success') {
                        showToast(data.message, 'success');
                        clearOtpInputs();
                        otpInputs[0].focus();
                        startResendTimer();
                    }
                } catch (error) {
                    showToast('Failed to resend OTP. Please try again.');
                }
            });

            // Navigation buttons
            backToEmailBtn.addEventListener('click', function() {
                showEmailForm();
                if (resendTimer) clearInterval(resendTimer);
            });

            backToOtpBtn.addEventListener('click', function() {
                showOtpForm(document.getElementById('hiddenEmailPassword').value);
            });
        });
    </script>
@endpush