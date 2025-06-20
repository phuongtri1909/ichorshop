@extends('client.layouts.information')

@section('info_title', 'Checkout - ' . request()->getHost())
@section('info_description', 'Complete your purchase at IchorShop')
@section('info_keyword', 'Checkout, Payment, ' . request()->getHost())
@section('info_section_title', 'Checkout')
@section('info_section_desc', 'Complete your order')

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'Checkout',
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'My Cart', 'url' => route('user.cart.index')],
            ['title' => 'Checkout', 'url' => '#'],
        ],
    ])
@endpush

@section('info_content')
    <div class="checkout-container">
        <form id="checkoutForm" action="{{ route('user.checkout.process') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-7">
                    <!-- Contact Info -->
                    <div class="section-container mb-4">
                        <div class="section-header d-flex align-items-center mb-3">
                            <div class="section-icon me-2">
                                <img src="{{ asset('assets/images/svg/user-border.svg') }}" alt="">
                            </div>
                            <h5 class="mb-0">CONTACT INFO</h5>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Your phone number <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email address <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="section-container mb-4">
                        <div class="section-header d-flex align-items-center mb-3">
                            <div class="section-icon me-2">
                                <img src="{{ asset('assets/images/svg/shipping-address.svg') }}" alt="">
                            </div>
                            <h5 class="mb-0">SHIPPING ADDRESS</h5>
                        </div>
                        <div class="section-body">
                            <!-- Saved Addresses -->
                            @if ($addresses->count() > 0)
                                <div class="saved-addresses mb-4">
                                    <label class="form-label">Select a saved address</label>
                                    <div class="row">
                                        @foreach ($addresses as $addr)
                                            <div class="col-12 mb-2">
                                                <div
                                                    class="address-card card {{ $address && $addr->id == $address->id ? 'border-primary' : '' }}">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input address-selector" type="radio"
                                                                name="saved_address" id="address{{ $addr->id }}"
                                                                value="{{ $addr->id }}"
                                                                {{ $address && $addr->id == $address->id ? 'checked' : '' }}
                                                                data-street="{{ $addr->street }}"
                                                                data-city-id="{{ $addr->city_id }}"
                                                                data-city="{{ $addr->city->name ?? '' }}"
                                                                data-state="{{ $addr->city->state->name ?? '' }}"
                                                                data-state-code="{{ $addr->city->state->code ?? '' }}"
                                                                data-country="{{ $addr->city->state->country->name ?? '' }}"
                                                                data-country-code="{{ $addr->city->state->country_code ?? '' }}"
                                                                data-postal-code="{{ $addr->postal_code }}">
                                                            <label class="form-check-label"
                                                                for="address{{ $addr->id }}">
                                                                <strong>{{ $addr->label }}</strong>
                                                                @if ($addr->is_default)
                                                                    <span class="badge bg-primary-1 ms-2">Default</span>
                                                                @endif
                                                                <div class="mt-1">
                                                                    {{ $addr->street }},
                                                                    {{ $addr->city->name ?? '' }},
                                                                    {{ $addr->city->state->name ?? '' }},
                                                                    {{ $addr->city->state->country->name ?? '' }},
                                                                    {{ $addr->postal_code }}
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="col-12 mt-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="saved_address"
                                                    id="newAddress" value="new" {{ !$address ? 'checked' : '' }}>
                                                <label class="form-check-label" for="newAddress">
                                                    Use a new shipping address
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- New Address Form -->
                            <div id="newAddressForm" class="{{ $addresses->count() > 0 && $address ? 'd-none' : '' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                            id="first_name" name="first_name"
                                            value="{{ old('first_name', $user->full_name ? explode(' ', $user->full_name)[0] : '') }}"
                                            required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                            id="last_name" name="last_name"
                                            value="{{ old('last_name', $user->full_name && strpos($user->full_name, ' ') !== false ? substr($user->full_name, strpos($user->full_name, ' ') + 1) : '') }}"
                                            required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address line 1 <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="address" name="address"
                                        value="{{ old('address', $address->street ?? '') }}"
                                        placeholder="Street address, P.O. box" required>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="apt" class="form-label">Address line 2</label>
                                    <input type="text" class="form-control @error('apt') is-invalid @enderror"
                                        id="apt" name="apt" value="{{ old('apt', $address->label ?? '') }}"
                                        placeholder="Apartment, suite, unit, building, floor, etc.">
                                    @error('apt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="country" class="form-label">Country <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('country') is-invalid @enderror" id="country"
                                            name="country" required>
                                            <option value="">Select country</option>

                                        </select>
                                        @error('country')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">State/Province <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('state') is-invalid @enderror" id="state"
                                            name="state" required>
                                            <option value="">Select state</option>
                                            <!-- States will be populated via AJAX -->
                                        </select>
                                        @error('state')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('city') is-invalid @enderror" id="city"
                                            name="city" required disabled>
                                            <option value="">Select city</option>
                                            <!-- Cities will be populated via AJAX -->
                                        </select>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="postal_code" class="form-label">Postal code <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('postal_code') is-invalid @enderror"
                                            id="postal_code" name="postal_code"
                                            value="{{ old('postal_code', $address->postal_code ?? '') }}" required>
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="section-container mb-4">
                        <div class="section-header d-flex align-items-center mb-3">
                            <div class="section-icon me-2">
                                <img src="{{ asset('assets/images/svg/payment.svg') }}" alt="">
                            </div>
                            <h5 class="mb-0">PAYMENT METHOD</h5>
                        </div>
                        <div class="section-body">
                            <div class="payment-methods">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="paymentMastercard" value="mastercard" checked>
                                    <label class="form-check-label payment-method-label" for="paymentMastercard">
                                        <div class="d-flex align-items-center">
                                            <div class="payment-icon me-3">
                                                <img src="https://cdn.iconscout.com/icon/free/png-256/mastercard-3521564-2944982.png"
                                                    alt="MasterCard" width="40">
                                            </div>
                                            <div>
                                                <strong>Credit/Debit Card</strong>
                                                <div class="text-muted">Pay securely with your credit or debit card</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="paymentPaypal" value="paypal">
                                    <label class="form-check-label payment-method-label" for="paymentPaypal">
                                        <div class="d-flex align-items-center">
                                            <div class="payment-icon me-3">
                                                <img src="https://cdn.iconscout.com/icon/free/png-256/paypal-54-675727.png"
                                                    alt="PayPal" width="40">
                                            </div>
                                            <div>
                                                <strong>PayPal</strong>
                                                <div class="text-muted">Fast and secure payment with PayPal</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Credit Card Form (will be shown/hidden based on selected payment method) -->
                            <div id="creditCardForm" class="mt-4">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="card_number" class="form-label">Card Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="card_number"
                                                placeholder="1234 5678 9012 3456">
                                            <span class="input-group-text">
                                                <div class="d-flex">
                                                    <img src="https://cdn.iconscout.com/icon/free/png-256/visa-3-226460.png"
                                                        alt="Visa" width="30" class="me-2">
                                                    <img src="https://cdn.iconscout.com/icon/free/png-256/mastercard-3521564-2944982.png"
                                                        alt="MasterCard" width="30">
                                                </div>
                                            </span>
                                        </div>
                                        <small class="text-muted">For demo purposes only. No actual payment will be
                                            processed.</small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_name" class="form-label">Name on Card</label>
                                        <input type="text" class="form-control" id="card_name"
                                            placeholder="John Doe">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="expiry" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiry" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>

                            <!-- PayPal Info (will be shown/hidden based on selected payment method) -->
                            <div id="paypalInfo" class="mt-4 d-none">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    You will be redirected to PayPal to complete your payment after placing the order.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="section-container mb-4">
                        <div class="section-header d-flex align-items-center mb-3">
                            <div class="section-icon me-2">
                                <i class="fa-regular fa-note-sticky fa-lg" style="color: #878787;"></i>
                            </div>
                            <h5 class="mb-0">ORDER NOTES</h5>
                        </div>
                        <div class="section-body">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Additional notes (optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"
                                    placeholder="Special instructions for delivery, etc.">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <!-- Order Summary -->
                    <div class="section-container order-summary mb-4" id="orderSummarySticky">
                        <div class="section-header d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="section-icon me-2">
                                    <img src="http://ichorshop.local/assets/images/svg/orders.svg" alt="Orders">
                                </div>
                                <h5 class="mb-0">ORDER SUMMARY</h5>
                            </div>
                            <span class="badge bg-primary-1">{{ $selectedItems->count() }} items</span>
                        </div>
                        <div class="section-body">
                            <!-- Selected Products -->
                            <div class="selected-products mb-3">
                                @foreach ($selectedItems as $item)
                                    <div class="selected-product d-flex py-2 border-bottom">
                                        <div class="product-image">
                                            <img src="{{ $item->product->avatar_url }}" alt="{{ $item->product->name }}"
                                                width="60" height="60" class="rounded"
                                                style="object-fit: scale-down">
                                            <span class="quantity-badge">{{ $item->quantity }}</span>
                                        </div>
                                        <div class="product-details ms-3 flex-grow-1">
                                            <div class="product-name fw-medium">{{ $item->product->name }}</div>
                                            <div class="product-variant small text-muted">
                                                @if ($item->variant)
                                                    @if ($item->variant->color)
                                                        <span class="color-dot me-1"
                                                            style="background-color: {{ $item->variant->color }};"></span>
                                                        <span class="me-1">{{ $item->variant->color_name }}</span>
                                                    @endif
                                                    @if ($item->variant->size)
                                                        <span class="size-label">Size: {{ $item->variant->size }}</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div class="product-price text-end">
                                            <div class="price fw-bold">${{ number_format($item->price, 2) }}</div>
                                            @if ($item->promotion_info)
                                                <div class="original-price text-muted text-decoration-line-through small">
                                                    ${{ number_format($item->promotion_info['original_price'], 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>Subtotal</div>
                                    <div class="fw-medium">${{ number_format($subtotal, 2) }}</div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <div>Shipping</div>
                                    <div class="fw-medium">${{ number_format($shippingCost, 2) }}</div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <div>Tax (10%)</div>
                                    <div class="fw-medium">${{ number_format($tax, 2) }}</div>
                                </div>
                                <div class="d-flex justify-content-between pt-2 mt-2 border-top">
                                    <div class="fw-bold">Total</div>
                                    <div class="fw-bold text-danger">${{ number_format($total, 2) }}</div>
                                </div>
                            </div>

                            <!-- Coupon Code (Optional for future) -->
                            <div class="coupon-form mt-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon-code"
                                        placeholder="Mã giảm giá">
                                    <button class="btn btn-outline-secondary" type="button" id="apply-coupon">
                                        <i class="fa-regular fa-hand-point-left"></i>
                                    </button>
                                </div>



                                <!-- Hiển thị thông tin mã giảm giá đã áp dụng -->
                                <div id="applied-coupon" class="mt-2 d-none">
                                    <div
                                        class="applied-coupon-info d-flex justify-content-between align-items-center p-2 border rounded">
                                        <div>
                                            <span class="badge bg-success">Mã giảm giá</span>
                                            <span id="coupon-code-display" class="ms-1"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="remove-coupon">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Place Order Button -->
                            <div class="place-order-btn mt-4">
                                <button type="submit" class="btn btn-pry btn-lg w-100">
                                    <i class="fas fa-lock me-2"></i>Place Order
                                </button>
                                <div class="text-center mt-2 small text-muted">
                                    By placing your order, you agree to our <a href="#"
                                        class="text-decoration-underline color-primary">Terms of Service</a> and <a
                                        href="#" class="text-decoration-underline color-primary">Privacy Policy</a>
                                </div>
                            </div>

                            <!-- Available coupons section -->
                            <div id="available-coupons" class="mt-3 d-none">
                                <p class="small fw-medium mb-2">Available coupons for you:</p>
                                <div class="available-coupon-list">
                                    <!-- Coupons will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Secure Payment Info -->
                    <div class="secure-payment-info text-center p-3 border rounded bg-light">
                        <div class="secure-icons mb-2">
                            <i class="fas fa-lock me-2"></i>
                            <i class="fab fa-cc-visa mx-1"></i>
                            <i class="fab fa-cc-mastercard mx-1"></i>
                            <i class="fab fa-cc-paypal mx-1"></i>
                        </div>
                        <div class="small text-muted">Your payment information is processed securely. We do not store
                            credit card details nor have access to your payment information.</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('info_scripts')
    <script>
        $(document).ready(function() {
            // Handle payment method change
            $('input[name="payment_method"]').on('change', function() {
                if ($(this).val() === 'mastercard') {
                    $('#creditCardForm').removeClass('d-none');
                    $('#paypalInfo').addClass('d-none');
                } else if ($(this).val() === 'paypal') {
                    $('#creditCardForm').addClass('d-none');
                    $('#paypalInfo').removeClass('d-none');
                }
            });

            // Address selection
            $('.address-selector').on('change', function() {
                if ($(this).is(':checked')) {
                    // Hide new address form when saved address is selected
                    $('#newAddressForm').addClass('d-none');

                    $('#newAddressForm input[required], #newAddressForm select[required]').removeAttr(
                        'required');

                    // Update card styles
                    $('.address-card').removeClass('border-primary');
                    $(this).closest('.address-card').addClass('border-primary');
                }
            });

            $('#newAddress').on('change', function() {
                if ($(this).is(':checked')) {
                    // Show new address form
                    $('#newAddressForm').removeClass('d-none');

                    $('#first_name, #last_name, #address, #country, #state, #city, #postal_code').attr(
                        'required', 'required');

                    // Remove highlight from all address cards
                    $('.address-card').removeClass('border-primary');
                }
            });

            function loadCountries() {
                $.ajax({
                    url: "{{ route('user.countries') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="">Select Country</option>';
                            response.data.forEach(country => {
                                const selected =
                                    "{{ old('country', $address->city->state->country->name ?? '') }}" ==
                                    country.name ? 'selected' : '';
                                options +=
                                    `<option value="${country.name}" data-code="${country.code}" ${selected}>${country.name}</option>`;
                            });
                            $('#country').html(options);

                            // Trigger change if a country is selected
                            if ($('#country').val()) {
                                $('#country').trigger('change');
                            }
                        }
                    },
                    error: function() {
                        showToast('Error loading countries', 'error');
                    }
                });
            }

            // Country, state, city dropdowns
            $('#country').on('change', function() {
                var countryCode = $(this).find(':selected').data('code');
                if (countryCode) {
                    loadStates(countryCode);
                } else {
                    $('#state').html('<option value="">Select state</option>');
                    $('#city').html('<option value="">Select city</option>');
                }
            });

            $('#state').on('change', function() {
                var stateId = $(this).val();
                if (stateId) {

                    loadCities(stateId);
                } else {
                    $('#city').html('<option value="">Select city</option>');
                }
            });

            function loadAvailableCoupons() {
                $.ajax({
                    url: "{{ route('user.coupon.available') }}",
                    type: "GET",
                    success: function(response) {
                        if (response.success && response.coupons.length > 0) {
                            // Show the available coupons section
                            $('#available-coupons').removeClass('d-none');

                            // Clear existing coupons
                            $('.available-coupon-list').empty();

                            // Add each coupon
                            response.coupons.forEach(function(coupon) {
                                const isEligible = coupon.is_eligible;
                                let couponHtml = `
                                    <div class="coupon-card mb-2 p-2 border rounded ${isEligible ? 'bg-light' : 'bg-light-gray'}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="${!isEligible ? 'text-muted' : ''}">
                                                <div class="fw-bold">
                                                    ${coupon.code} 
                                                    <span class="ms-1 ${isEligible ? 'text-success' : 'text-muted'}">${coupon.display_value}</span>
                                                </div>
                                                <div class="small text-muted">${coupon.description || 'Discount applied to eligible items'}</div>
                                                <div class="small">
                                                    ${coupon.applicable_items < coupon.total_items ? 
                                                        `Applies to ${coupon.applicable_items} of ${coupon.total_items} items` : 
                                                        'Applies to all items'}
                                                </div>
                                                ${coupon.min_order_amount > 0 ? 
                                                    `<div class="small">Min order: $${parseFloat(coupon.min_order_amount).toFixed(2)}</div>` : ''}
                                                ${coupon.end_date ? 
                                                    `<div class="small text-danger">Expires: ${coupon.end_date}</div>` : ''}
                                                ${!isEligible ? 
                                                    `<div class="small text-danger mt-1"><i class="fas fa-exclamation-circle"></i> ${coupon.ineligibility_reason}</div>` : ''}
                                            </div>
                                            <button type="button" 
                                                class="btn btn-sm ${isEligible ? 'btn-pry' : 'btn-outline-secondary'} apply-coupon-btn" 
                                                data-coupon="${coupon.code}"
                                                ${!isEligible ? 'disabled' : ''}>
                                                ${isEligible ? 'Apply' : 'Apply'}
                                            </button>
                                        </div>
                                    </div>
                                `;
                                $('.available-coupon-list').append(couponHtml);
                            });

                            // Attach click event to apply buttons
                            $('.apply-coupon-btn').on('click', function() {
                                const couponCode = $(this).data('coupon');
                                $('#coupon-code').val(couponCode);
                                $('#apply-coupon').trigger('click');
                            });
                        }
                    },
                    error: function(xhr) {
                        // Handle error silently - just don't show available coupons
                        console.log("Error loading available coupons:", xhr.responseJSON?.message || xhr
                            .statusText);
                    }
                });
            }

            loadAvailableCoupons();

            // Load states for selected country
            function loadStates(countryCode) {
                $.ajax({
                    url: "{{ route('user.states') }}",
                    type: "GET",
                    data: {
                        country_code: countryCode
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            var stateSelect = $('#state');
                            stateSelect.html('<option value="">Select state</option>');
                            stateSelect.prop('disabled', false);

                            if (response.data && response.data.length > 0) {
                                $.each(response.data, function(key, state) {
                                    const selected =
                                        "{{ old('state', $address->city->state->name ?? '') }}" ==
                                        state.name ? 'selected' : '';
                                    stateSelect.append(
                                        `<option value="${state.id}" data-id="${state.id}" ${selected}>${state.name}</option>`
                                    );
                                });

                                // Trigger change if a state is selected
                                if (stateSelect.val()) {
                                    stateSelect.trigger('change');
                                }
                            }
                        }
                    },
                    error: function() {
                        showToast('Error loading states', 'error');
                    }
                });
            }

            // Load cities for selected state
            function loadCities(stateId) {
                $.ajax({
                    url: "{{ route('user.cities') }}",
                    type: "GET",
                    data: {
                        state_id: stateId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            var citySelect = $('#city');
                            citySelect.html('<option value="">Select city</option>');
                            citySelect.prop('disabled', false);

                            if (response.data && response.data.length > 0) {
                                $.each(response.data, function(key, city) {
                                    const selected =
                                        "{{ old('city', $address->city->name ?? '') }}" == city
                                        .name ? 'selected' : '';
                                    citySelect.append(
                                        `<option value="${city.name}" data-id="${city.id}" ${selected}>${city.name}</option>`
                                    );
                                });
                            }
                        }
                    },
                    error: function() {
                        showToast('Error loading cities', 'error');
                    }
                });
            }

            loadCountries();

            // Make order summary sticky on scroll
            var stickyOffset = $('#orderSummarySticky').offset().top;
            $(window).scroll(function() {
                var sticky = $('#orderSummarySticky'),
                    scroll = $(window).scrollTop();

                if (scroll >= stickyOffset && $(window).width() >= 992) {
                    sticky.addClass('sticky-summary');
                } else {
                    sticky.removeClass('sticky-summary');
                }
            });

            // Handle form submission
            $('#checkoutForm').on('submit', function(e) {
                // If using saved address, get the address details from the selected option
                if ($('input[name="saved_address"]:checked').val() && $(
                        'input[name="saved_address"]:checked').val() !== 'new') {
                    var selectedAddress = $('input[name="saved_address"]:checked');

                    // Update form fields with selected address data
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'address',
                        value: selectedAddress.data('street')
                    }).appendTo(this);

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'city',
                        value: selectedAddress.data('city')
                    }).appendTo(this);

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'state',
                        value: selectedAddress.data('state')
                    }).appendTo(this);

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'country',
                        value: selectedAddress.data('country')
                    }).appendTo(this);

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'postal_code',
                        value: selectedAddress.data('postal-code')
                    }).appendTo(this);

                    // Thêm first_name, last_name từ user hiện tại
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'first_name',
                        value: "{{ $user->full_name ? explode(' ', $user->full_name)[0] : '' }}"
                    }).appendTo(this);

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'last_name',
                        value: "{{ $user->full_name && strpos($user->full_name, ' ') !== false ? substr($user->full_name, strpos($user->full_name, ' ') + 1) : '' }}"
                    }).appendTo(this);
                }


                // For demo purposes, prevent actual credit card submission but allow form to proceed
                if ($('input[name="payment_method"]:checked').val() === 'mastercard') {
                    // Clear any actual card data before submitting
                    $('#card_number').val('');
                    $('#card_name').val('');
                    $('#expiry').val('');
                    $('#cvv').val('');
                }

                return true;
            });
        });



        // Áp dụng mã giảm giá
        $('#apply-coupon').on('click', function() {
            const couponCode = $('#coupon-code').val().trim();

            if (!couponCode) {
                showToast('Please enter a coupon code.', 'warning');
                return;
            }

            // Gửi yêu cầu áp dụng mã giảm giá
            $.ajax({
                url: "{{ route('user.coupon.apply') }}",
                type: "POST",
                data: {
                    code: couponCode,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    showToast(response.message, 'success');

                    $('#applied-coupon').removeClass('d-none');
                    $('#coupon-code-display').text(couponCode);

                    // Cập nhật giá
                    updatePrices(response.discount);

                    // Xóa input
                    $('#coupon-code').val('');

                    // Thêm input ẩn để lưu mã giảm giá khi submit form
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'applied_coupon',
                        value: couponCode
                    }).appendTo('#checkoutForm');

                    // Thêm các dữ liệu khác nếu cần
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'coupon_discount',
                        value: response.discount
                    }).appendTo('#checkoutForm');
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred, please try again later.';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    showToast(errorMessage, 'error');
                }
            });
        });

        // Xóa mã giảm giá
        $('#remove-coupon').on('click', function() {
            $.ajax({
                url: "{{ route('user.coupon.remove') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    // Ẩn phần hiển thị mã giảm giá
                    $('#applied-coupon').addClass('d-none');
                    $('#coupon-code-display').text('');

                    showToast(response.message, 'success');

                    // Xóa các input ẩn
                    $('input[name="applied_coupon"]').remove();
                    $('input[name="coupon_discount"]').remove();

                    // Cập nhật giá
                    updatePrices(0);
                }
            });
        });

        // Cập nhật giá hiển thị
        function updatePrices(discount) {
            const subtotal = {{ $subtotal }};
            const shippingCost = {{ $shippingCost }};
            const tax = {{ $tax }};

            discount = parseFloat(discount) || 0;

            // Tính lại tổng tiền
            const total = subtotal + shippingCost + tax - discount;

            // Cập nhật hiển thị
            if (discount > 0) {
                // Thêm hoặc cập nhật hàng giảm giá
                if ($('#discount-row').length) {
                    $('#discount-amount').text('-$' + discount.toFixed(2));
                } else {
                    const discountRow = `
                        <div class="d-flex justify-content-between mb-2" id="discount-row">
                            <div>Giảm giá</div>
                            <div class="fw-medium text-success" id="discount-amount">-$${discount.toFixed(2)}</div>
                        </div>
                    `;
                    $(discountRow).insertBefore('.price-breakdown .border-top').parent();
                }
            } else {
                // Xóa hàng giảm giá nếu không còn áp dụng
                $('#discount-row').remove();
            }

            // Cập nhật tổng tiền
            $('.price-breakdown .fw-bold.text-danger').text('$' + total.toFixed(2));
        }
    </script>
@endpush

@push('styles')
    <style>
        .section-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.25rem;
            color: #333;
        }

        .address-card {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #dee2e6;
        }

        .address-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .address-card.border-primary {
            border-width: 2px;
        }

        .payment-method-label {
            display: block;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-method-label:hover {
            border-color: var(--primary-color);
            background-color: #f8f9fa;
        }

        input[type="radio"]:checked+.payment-method-label {
            border-color: var(--primary-color);
            background-color: #f0f8ff;
        }

        .selected-product {
            position: relative;
        }

        .product-image {
            position: relative;
        }

        .quantity-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .color-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        /* Sticky order summary */
        .sticky-summary {
            position: sticky;
            top: 20px;
        }

        @media (max-width: 992px) {
            .sticky-summary {
                position: relative;
                top: 0;
            }
        }

        .coupon-card {
            transition: all 0.2s ease;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .coupon-card:hover {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .coupon-card.bg-light-gray {
            background-color: #f2f2f2;
        }

        .coupon-card.bg-light-gray:hover {
            border-color: #dee2e6;
            box-shadow: none;
        }

        .apply-coupon-btn {
            white-space: nowrap;
        }
    </style>
@endpush
