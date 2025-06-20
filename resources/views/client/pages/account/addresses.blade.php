@extends('client.layouts.information')

@section('info_title', 'My Addresses - ' . request()->getHost())
@section('info_description', 'Manage your delivery addresses')
@section('info_keyword', 'My Addresses, Delivery Address, Shipping Address')
@section('info_section_title', 'My Addresses')
@section('info_section_desc', 'Manage your delivery addresses for easier checkout')

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'Addresses',
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'My Account', 'url' => route('user.my.account')],
            ['title' => 'Addresses', 'url' => route('user.addresses')], 
        ]
    ])
@endpush

@section('info_content')
    <div class="addresses-container">
        <!-- Add New Address Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="mb-0">Your Addresses</h6>
            <button type="button" class="btn btn-pry" id="addAddressBtn">
                <i class="fas fa-plus me-2"></i>Add New Address
            </button>
        </div>

        <!-- Addresses List -->
        <div class="addresses-list" id="addressesList">
            <!-- Addresses will be loaded here -->
        </div>

        <!-- Empty State -->
        <div class="empty-addresses d-none" id="emptyAddresses">
            <div class="text-center py-5">
                <i class="fas fa-map-marker-alt empty-icon"></i>
                <h5 class="mt-3">No addresses yet</h5>
                <p class="text-muted">Add your first delivery address to get started</p>
                <button type="button" class="btn btn-pry" onclick="$('#addAddressBtn').click()">
                    <i class="fas fa-plus me-2"></i>Add Address
                </button>
            </div>
        </div>
    </div>

    <!-- Add/Edit Address Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addressForm">
                        @csrf
                        <input type="hidden" id="addressId" name="address_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-select" id="country" name="country_code" required>
                                        <option value="">Select Country</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="state" class="form-label">State</label>
                                    <select class="form-select" id="state" name="state_id" required disabled>
                                        <option value="">Select State</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <select class="form-select" id="city" name="city_id" required disabled>
                                        <option value="">Select City</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" placeholder="Enter postal code">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="street" class="form-label">Street Address</label>
                            <textarea class="form-control" id="street" name="street" rows="3"
                                placeholder="Enter full street address including house number, street name, etc." required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label for="address_label" class="form-label">Address Label (Optional)</label>
                            <input type="text" class="form-control" id="address_label" name="label"
                                placeholder="e.g., Home, Office, etc.">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input mx-2" type="checkbox" id="is_default" name="is_default">
                            <label class="form-check-label" for="is_default">
                                Set as default address
                            </label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-pry" id="saveAddressBtn">
                        <span class="btn-text">Save Address</span>
                        <span class="btn-spinner d-none">
                            <i class="fas fa-spinner fa-spin me-2"></i>Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this address? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="btn-text">Delete</span>
                        <span class="btn-spinner d-none">
                            <i class="fas fa-spinner fa-spin me-2"></i>Deleting...
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
            let currentAddressId = null;

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
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').text('').hide();
            }

            // Show field error
            function showFieldError(fieldName, message) {
                const field = $(`[name="${fieldName}"]`);
                let feedback = field.siblings('.invalid-feedback');

                if (feedback.length === 0) {
                    feedback = field.closest('.mb-3').find('.invalid-feedback');
                }

                field.addClass('is-invalid');

                if (feedback.length > 0) {
                    feedback.text(message).show();
                } else {
                    field.after(`<div class="invalid-feedback" style="display: block;">${message}</div>`);
                }
            }

            // Load addresses
            function loadAddresses() {
                $.ajax({
                    url: "{{ route('user.addresses.list') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            renderAddresses(response.data);
                        }
                    },
                    error: function() {
                        showToast('Error loading addresses');
                    }
                });
            }

            // Render addresses
            function renderAddresses(addresses) {
                const container = $('#addressesList');
                const emptyState = $('#emptyAddresses');

                if (addresses.length === 0) {
                    container.empty();
                    emptyState.removeClass('d-none');
                    return;
                }

                emptyState.addClass('d-none');
                let html = '';

                addresses.forEach(address => {
                    const cityName = address.city ? address.city.name : '';
                    const stateName = address.city && address.city.state ? address.city.state.name : '';
                    const countryName = address.city && address.city.state && address.city.state.country ?
                        address.city.state.country.name : '';

                    html += `
                        <div class="address-card" data-id="${address.id}">
                            <div class="address-content">
                                <div class="address-header">
                                    <h6 class="address-label">${address.label || 'Address'}</h6>
                                    ${address.is_default ? '<span class="badge bg-primary">Default</span>' : ''}
                                </div>
                                <div class="address-details">
                                    <p class="street">${address.street}</p>
                                    <p class="location">${cityName}${stateName ? ', ' + stateName : ''}${countryName ? ', ' + countryName : ''}</p>
                                    ${address.postal_code ? `<p class="postal-code">Postal Code: ${address.postal_code}</p>` : ''}
                                </div>
                            </div>
                            <div class="address-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-address" data-id="${address.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                ${!address.is_default ? `
                                        <button type="button" class="btn btn-sm btn-outline-success set-default" data-id="${address.id}">
                                            <i class="fas fa-check"></i> Set Default
                                        </button>
                                    ` : ''}
                                <button type="button" class="btn btn-sm btn-outline-danger delete-address" data-id="${address.id}">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    `;
                });

                container.html(html);
            }

            // Load countries
            function loadCountries() {
                $.ajax({
                    url: "{{ route('user.countries') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="">Select Country</option>';
                            response.data.forEach(country => {
                                options +=
                                    `<option value="${country.code}">${country.name}</option>`;
                            });
                            $('#country').html(options);
                        }
                    },
                    error: function() {
                        showToast('Error loading countries');
                    }
                });
            }

            // Load states based on country
            function loadStates(countryCode, selectedStateId = null) {
                const stateSelect = $('#state');
                const citySelect = $('#city');

                if (!countryCode) {
                    stateSelect.html('<option value="">Select State</option>').prop('disabled', true);
                    citySelect.html('<option value="">Select City</option>').prop('disabled', true);
                    return;
                }

                $.ajax({
                    url: "{{ route('user.states') }}",
                    type: 'GET',
                    data: {
                        country_code: countryCode
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="">Select State</option>';
                            response.data.forEach(state => {
                                const selected = selectedStateId == state.id ? 'selected' : '';
                                options +=
                                    `<option value="${state.id}" ${selected}>${state.name}</option>`;
                            });
                            stateSelect.html(options).prop('disabled', false);

                            if (selectedStateId) {
                                stateSelect.trigger('change');
                            }
                        }
                    },
                    error: function() {
                        showToast('Error loading states');
                    }
                });
            }

            // Load cities based on state
            function loadCities(stateId, selectedCityId = null) {
                const citySelect = $('#city');

                if (!stateId) {
                    citySelect.html('<option value="">Select City</option>').prop('disabled', true);
                    return;
                }

                $.ajax({
                    url: "{{ route('user.cities') }}",
                    type: 'GET',
                    data: {
                        state_id: stateId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            let options = '<option value="">Select City</option>';
                            response.data.forEach(city => {
                                const selected = selectedCityId == city.id ? 'selected' : '';
                                options +=
                                    `<option value="${city.id}" ${selected}>${city.name}</option>`;
                            });
                            citySelect.html(options).prop('disabled', false);
                        }
                    },
                    error: function() {
                        showToast('Error loading cities');
                    }
                });
            }

            // Event handlers
            $('#country').on('change', function() {
                const countryCode = $(this).val();
                loadStates(countryCode);
                $('#city').html('<option value="">Select City</option>').prop('disabled', true);
            });

            $('#state').on('change', function() {
                const stateId = $(this).val();
                
                loadCities(stateId);
            });

            // Add new address
            $('#addAddressBtn').on('click', function() {
                currentAddressId = null;
                $('#addressModalLabel').text('Add New Address');
                $('#addressForm')[0].reset();
                $('#addressId').val('');
                $('#state').prop('disabled', true);
                $('#city').prop('disabled', true);
                clearErrors();
                loadCountries();
                $('#addressModal').modal('show');
            });

            // Edit address - FIXED
            $(document).on('click', '.edit-address', function() {
                const addressId = $(this).data('id');
                currentAddressId = addressId;
                $('#addressModalLabel').text('Edit Address');

                // Use Laravel route helper correctly
                const url = "{{ route('user.addresses.show', ':id') }}".replace(':id', addressId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            const address = response.data;
                            $('#addressId').val(address.id);
                            $('#street').val(address.street);
                            $('#postal_code').val(address.postal_code || '');
                            $('#address_label').val(address.label || '');
                            $('#is_default').prop('checked', address.is_default);

                            loadCountries();
                            setTimeout(() => {
                                if (address.city && address.city.state) {
                                    $('#country').val(address.city.state.country_code);
                                    loadStates(address.city.state.country_code, address
                                        .city.state.id);
                                    setTimeout(() => {
                                        loadCities(address.city.state.id,
                                            address.city.id);
                                    }, 500);
                                }
                            }, 500);

                            $('#addressModal').modal('show');
                        }
                    },
                    error: function() {
                        showToast('Error loading address details');
                    }
                });
            });

            // Save address - FIXED
            $('#saveAddressBtn').on('click', function() {
                clearErrors();
                const saveBtn = $(this);
                toggleLoading(saveBtn, true);

                let formData = $('#addressForm').serialize();
                let url, method;

                if (currentAddressId) {
                    // Update existing address
                    url = "{{ route('user.addresses.update', ':id') }}".replace(':id', currentAddressId);
                    method = 'PUT';
                    // Add method override for PUT request
                    const methodField = '<input type="hidden" name="_method" value="PUT">';
                    formData += '&' + methodField.split(' ').join('').replace('name=', '').replace('value=',
                        '=').replace('"', '').replace('"', '').replace('>', '').replace(
                        '<inputtype=hidden', '_method').replace('>', '');
                } else {
                    // Create new address
                    url = "{{ route('user.addresses.store') }}";
                    method = 'POST';
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            $('#addressModal').modal('hide');
                            loadAddresses();
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
                            showToast('Error saving address', 'error');
                        }
                    },
                    complete: function() {
                        toggleLoading(saveBtn, false);
                    }
                });
            });

            // Set default address - FIXED
            $(document).on('click', '.set-default', function() {
                const addressId = $(this).data('id');
                const url = "{{ route('user.addresses.default', ':id') }}".replace(':id', addressId);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            loadAddresses();
                        }
                    },
                    error: function() {
                        showToast('Error setting default address');
                    }
                });
            });

            // Delete address - FIXED
            $(document).on('click', '.delete-address', function() {
                currentAddressId = $(this).data('id');
                $('#deleteModal').modal('show');
            });

            $('#confirmDeleteBtn').on('click', function() {
                const deleteBtn = $(this);
                toggleLoading(deleteBtn, true);

                const url = "{{ route('user.addresses.delete', ':id') }}".replace(':id', currentAddressId);

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            $('#deleteModal').modal('hide');
                            loadAddresses();
                        }
                    },
                    error: function() {
                        showToast('Error deleting address');
                    },
                    complete: function() {
                        toggleLoading(deleteBtn, false);
                    }
                });
            });

            // Reset modal when closed
            $('#addressModal').on('hidden.bs.modal', function() {
                $('#addressForm')[0].reset();
                clearErrors();
                currentAddressId = null;
                $('#state').prop('disabled', true);
                $('#city').prop('disabled', true);
            });

            // Initialize
            loadAddresses();
        });
    </script>
@endpush

@push('styles')
    <style>
        /* Address Management Styles */
        .addresses-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .address-card {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: all 0.2s ease;
        }

        .address-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 2px 8px rgba(var(--primary-rgb), 0.1);
        }

        .address-content {
            flex: 1;
        }

        .address-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .address-label {
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .address-details p {
            margin: 0;
            color: #666;
            line-height: 1.4;
        }

        .address-details .street {
            color: #333;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .address-details .location {
            margin-bottom: 4px;
        }

        .address-details .postal-code {
            font-size: 14px;
        }

        .address-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 120px;
        }

        .address-actions .btn {
            white-space: nowrap;
        }

        .empty-addresses {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 16px;
        }

        /* Form Styles for Modal */
        .modal-lg {
            max-width: 600px;
        }

        .form-check {
            padding: 12px 0;
            border-top: 1px solid #e5e5e5;
            margin-top: 8px;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Badge Styles */
        .badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .address-card {
                flex-direction: column;
                gap: 16px;
            }

            .address-actions {
                flex-direction: row;
                flex-wrap: wrap;
                min-width: auto;
            }

            .address-actions .btn {
                flex: 1;
                min-width: 80px;
            }

            .addresses-container {
                padding: 16px;
            }
        }
    </style>
@endpush
