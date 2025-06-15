<footer class="footer-section">
    <!-- Newsletter Section -->
    <div class="newsletter-section">
        <div class="container">
            <div class="newsletter-content p-0 p-md-4">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3 class="newsletter-title mb-0 text-start">STAY UPTO DATE ABOUT OUR LATEST OFFERS</h3>
                    </div>
                    <div class="col-lg-6 mt-3 mt-lg-0">
                        <div class="newsletter-form d-flex flex-column align-items-end gap-3">
                            <div class="input-group" style="width: 350px;">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" placeholder="Enter your email address">
                            </div>
                            <button type="submit" class="btn newsletter-btn" style="width: 350px;">Subscribe to
                                Newsletter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer -->
    <div class="main-footer">
        <div class="container-md">
            <div class="row">
                <!-- Brand Section -->
                <div class="col-12 col-md-4">
                    <div class="footer-brand">
                        <a class="navbar-brand p-0" href="{{ route('home') }}">
                            <img height="70" src="{{ $logoPath }}" alt="{{ config('app.name') }} logo">
                        </a>
                        <p class="brand-description">
                            We have clothes that suits your style and which you're proud to wear. From women to men.
                        </p>
                        <div class="social-icons">
                           
                            @foreach ($socials as $social)
                                <a href="{{ $social->link }}" class="social-link">
                                    <i class="{{ $social->icon }}"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Company Links -->
                <div class="col-6 col-md-4 col-lg-2 mt-4">
                    <div class="footer-links">
                        <h6 class="footer-title">COMPANY</h6>
                        <ul class="links-list">
                            <li><a href="#">About</a></li>
                            <li><a href="#">Features</a></li>
                            <li><a href="#">Works</a></li>
                            <li><a href="#">Career</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Help Links -->
                <div class="col-6 col-md-4 col-lg-2 mt-4">
                    <div class="footer-links">
                        <h6 class="footer-title">HELP</h6>
                        <ul class="links-list">
                            <li><a href="#">Customer Support</a></li>
                            <li><a href="#">Delivery Details</a></li>
                            <li><a href="#">Terms & Conditions</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-md-4 d-lg-none">
                   
                </div>

                <!-- FAQ Links -->
                <div class="col-6 col-md-4 col-lg-2 mt-4">
                    <div class="footer-links">
                        <h6 class="footer-title">FAQ</h6>
                        <ul class="links-list">
                            <li><a href="#">Account</a></li>
                            <li><a href="#">Manage Deliveries</a></li>
                            <li><a href="#">Orders</a></li>
                            <li><a href="#">Payments</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Resources Links -->
                <div class="col-6 col-md-4 col-lg-2 mt-4">
                    <div class="footer-links">
                        <h6 class="footer-title">RESOURCES</h6>
                        <ul class="links-list">
                            <li><a href="#">Free eBooks</a></li>
                            <li><a href="#">Development Tutorial</a></li>
                            <li><a href="#">How to - Blog</a></li>
                            <li><a href="#">Youtube Playlist</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright">Shop.co © 2000-2023, All Rights Reserved</p>
                    </div>
                    <div class="col-md-6">
                        <div class="payment-methods">
                            <img src="{{ asset('assets/images/svg/Visa.svg') }}" alt="Visa">
                            <img src="{{ asset('assets/images/svg/Mastercard.svg') }}" alt="Mastercard">
                            <img src="{{ asset('assets/images/svg/Paypal.svg') }}" alt="PayPal">
                            <img src="{{ asset('assets/images/svg/Applepay.svg') }}" alt="Apple Pay">
                            <img src="{{ asset('assets/images/svg/GPay.svg') }}" alt="Google Pay">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
@stack('scripts')
</body>

</html>
