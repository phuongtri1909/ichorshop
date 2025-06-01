<section class="features-section py-5">
    <div class="container">
        <div class="row">
            <!-- Left Side Content -->
            <div class="col-lg-4 col-md-12 mb-5 mb-lg-0 d-flex align-items-center">
                <div class="features-intro">
                    <h3 class="features-title">Why choose products with</h3>
                    <img height="70" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
                    <p class="features-description">Lorem ipsum det, cowec tetur duis necgi det, consec t eturlagix adipiscing eliet duis necgi det, con</p>
                    <a href="#" class="btn btn-features">View All Features <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>

            <!-- Right Side Features Grid -->
            <div class="col-lg-8 col-md-12">
                <div class="row g-4">
                    <!-- Feature 1 -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon mb-3">
                                <img src="{{ asset('assets/images/svg/features1.svg') }}" alt="No Die & Plate Charges" class="img-fluid">
                            </div>
                            <h4 class="feature-title">NO Die & plate charges</h4>
                            <p class="feature-description">Lorem ipsum det, cowec tetur duis necgi det, consect eturlagix adipiscing eliet duis necgi det, con</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon mb-3">
                                <img src="{{ asset('assets/images/svg/features2.svg') }}" alt="High Quality Offset Printing" class="img-fluid">
                            </div>
                            <h4 class="feature-title">High quality offset printing</h4>
                            <p class="feature-description">Lorem ipsum det, cowec tetur duis necgi det, consect eturlagix adipiscing eliet duis necgi det, con</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon mb-3">
                                <img src="{{ asset('assets/images/svg/features3.svg') }}" alt="Secure Payment" class="img-fluid">
                            </div>
                            <h4 class="feature-title">Secure payment</h4>
                            <p class="feature-description">Lorem ipsum det, cowec tetur duis necgi det, consect eturlagix adipiscing eliet duis necgi det, con</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon mb-3">
                                <img src="{{ asset('assets/images/svg/features4.svg') }}" alt="Custom Size & Style" class="img-fluid">
                            </div>
                            <h4 class="feature-title">Custom size & style</h4>
                            <p class="feature-description">Lorem ipsum det, cowec tetur duis necgi det, consect eturlagix adipiscing eliet duis necgi det, con</p>
                        </div>
                    </div>

                    <!-- Feature 5 -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon mb-3">
                                <img src="{{ asset('assets/images/svg/features5.svg') }}" alt="Fast & Free Delivery" class="img-fluid">
                            </div>
                            <h4 class="feature-title">Fast & free delivery</h4>
                            <p class="feature-description">Lorem ipsum det, cowec tetur duis necgi det, consect eturlagix adipiscing eliet duis necgi det, con</p>
                        </div>
                    </div>

                    <!-- Feature 6 -->
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon mb-3">
                                <img src="{{ asset('assets/images/svg/features6.svg') }}" alt="Low Minimum Order Quantity" class="img-fluid">
                            </div>
                            <h4 class="feature-title">Low minimum order quantity</h4>
                            <p class="feature-description">Lorem ipsum det, cowec tetur duis necgi det, consect eturlagix adipiscing eliet duis necgi det, con</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
    <style>
        /* Features Section với split background */
        .features-section {
            background: linear-gradient(180deg, var(--primary-color-4) 50%, var(--primary-color-2) 50%);
            position: relative;
        }

        .features-intro {
            padding-right: 2rem;
        }

        .features-title {
            font-size: 3rem;
            font-weight: 700;
            color: #000;
            line-height: 1.2;
        }

        .features-description {
            color: #6c757d;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-features {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-features:hover {
            background-color: #333;
            color: #fff;
            transform: translateY(-2px);
        }

        .feature-card {
            background: #fff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            height: 100%;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .feature-icon i {
            font-size: 24px;
            color: #000;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #000;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .feature-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .features-title {
                font-size: 2.5rem;
            }
            
            .features-intro {
                padding-right: 0;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .features-title {
                font-size: 2rem;
            }
            
            .feature-card {
                padding: 1.5rem;
            }
        }
    </style>
@endpush