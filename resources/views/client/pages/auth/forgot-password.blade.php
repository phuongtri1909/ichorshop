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
                        <form action="{{ route('forgot.password') }}" method="post">
                            @csrf

                           <span>Please enter the email address associated with your account. We'll promptly send you a link to reset your password.</span>

                            <div class="my-3">
                                <label for="email" class="form-label color-primary-3">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" id="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                           
                            <button type="submit" class="auth-btn btn w-100">Send reset link</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
