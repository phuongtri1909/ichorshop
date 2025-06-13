<section class="product-tabs-section py-5">
    <div class="container">
        <!-- Tab Navigation -->
        <div class="tab-navigation">
            <ul class="nav nav-tabs border-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-1 px-0 py-md-3" data-bs-toggle="tab" data-bs-target="#details"
                        type="button" role="tab">
                        Product Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1 px-0 py-md-3" data-bs-toggle="tab" data-bs-target="#reviews"
                        type="button" role="tab">
                        Rating & Reviews
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-1 px-0 py-md-3" data-bs-toggle="tab" data-bs-target="#faqs"
                        type="button" role="tab">
                        FAQs
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content mt-4">
            <!-- Product Details -->
            <div class="tab-pane fade show active" id="details" role="tabpanel">
                <div class="product-details-content">
                    <div class="description-container">
                        <div class="description-content text-muted mt-4 mb-0 text-justify"
                            id="description-content-{{ $product['id'] }}">
                            {!! $product['description_long'] !!}
                        </div>
                        <div class="description-toggle-btn mt-2 text-center d-none">
                            <button class="btn btn-sm btn-link show-more-btn">See More <i
                                    class="fas fa-chevron-down"></i></button>
                            <button class="btn btn-sm btn-link show-less-btn d-none">See Less <i
                                    class="fas fa-chevron-up"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div class="tab-pane fade" id="reviews" role="tabpanel">
                @include('components.product-reviews', [
                    'testimonials' => [
                        [
                            'name' => 'Alex K. ✓',
                            'rating' => 5,
                            'text' =>
                                '"Finding clothes that align with my personal style used to be a challenge until I discovered Shop.co. The range of options they offer is truly remarkable."',
                        ],
                        [
                            'name' => 'James L. ✓',
                            'rating' => 5,
                            'text' =>
                                '"As someone who\'s always on the lookout for unique fashion pieces, I\'m thrilled to have stumbled upon Shop.co. The selection of clothes is not only diverse but also on-point with the latest trends."',
                        ],
                        [
                            'name' => 'Sarah M. ✓',
                            'rating' => 5,
                            'text' =>
                                '"I\'m blown away by the quality and style of the clothes I received from Shop.co. From casual wear to elegant dresses, every piece I\'ve bought has exceeded my expectations."',
                        ],
                        [
                            'name' => 'Emily R. ✓',
                            'rating' => 5,
                            'text' =>
                                '"Shop.co has become my go-to destination for fashion. The variety of styles available means I can always find something that suits my mood and occasion."',
                        ],
                        [
                            'name' => 'Michael T. ✓',
                            'rating' => 5,
                            'text' =>
                                '"I appreciate the attention to detail in the clothing offered by Shop.co. The fabrics are high-quality, and the designs are both trendy and timeless."',
                        ],
                        [
                            'name' => 'Olivia S. ✓',
                            'rating' => 5,
                            'text' =>
                                '"Shopping at Shop.co has transformed my wardrobe. The clothes are not only stylish but also comfortable, making them perfect for everyday wear."',
                        ],
                    ],
                ])
            </div>

            <!-- FAQs -->
            <div class="tab-pane fade" id="faqs" role="tabpanel">
                <div class="faqs-content row">
                    <div class="faq-item col-12 col-md-6">
                        <h6>How does the sizing run?</h6>
                        <p>Our sizes run true to size. Please refer to our size chart for detailed measurements.</p>
                    </div>
                    <div class="faq-item col-12 col-md-6">
                        <h6>What is the return policy?</h6>
                        <p>We offer free returns within 30 days of purchase. Items must be in original condition.</p>
                    </div>
                    <div class="faq-item col-12 col-md-6">
                        <h6>How long does shipping take?</h6>
                        <p>Standard shipping takes 3-5 business days. Express shipping options are available.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
    <style>
        /* Product Tabs */
        .tab-navigation {
            border-bottom: 1px solid #e5e5e5;
        }

        .tab-navigation .nav-tabs {
            justify-content: space-between;
            border-bottom: none;
            width: 100%;
        }

        .tab-navigation .nav-item {
            flex: 1;
            text-align: center;
        }

        .tab-navigation .nav-link {
            color: var(--primary-color-5);
            background: none;
            border: none;
            font-size: 16px;
            position: relative;
            width: 100%;
            transition: all 0.3s ease;
        }

        .tab-navigation .nav-link:hover {
            color: var(--primary-color);
        }

        .tab-navigation .nav-link.active {
            color: var(--primary-color);
            background: none;
            border: none;
        }

        .tab-navigation .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-color);
        }

        /* FAQs */
        .faqs-content .faq-item {
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .faqs-content .faq-item h6 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .faqs-content .faq-item p {
            color: var(--primary-color-5);
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .tab-navigation .nav-link {
                font-size: 14px;
            }
        }

        .description-container {
            position: relative;
            max-width: 100%;
        }

        .description-content {
            max-height: 180px;
            /* Chiều cao tối đa khi thu gọn */
            overflow: hidden;
            position: relative;
            transition: max-height 0.5s ease;
            line-height: 1.8;
            text-align: justify;
        }
        
        .description-content.expanded {
            max-height: 5000px;
            /* Đủ lớn để chứa mọi nội dung */
        }
        
        .description-content:not(.expanded)::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: linear-gradient(transparent, #fff);
            pointer-events: none;
        }
        
        .description-toggle-btn .btn-link {
            color: var(--primary-color);
            text-decoration: none;
            padding: 5px 15px;
            border-radius: 15px;
            background-color: rgba(var(--primary-color-rgb), 0.05);
            transition: all 0.3s ease;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        
        .description-toggle-btn .btn-link:hover {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Description show more/less functionality
        function initDescriptionToggle() {
            const descriptionContent = document.getElementById('description-content-{{ $product['id'] }}');
            const toggleBtnContainer = document.querySelector('.description-toggle-btn');
            const showMoreBtn = document.querySelector('.show-more-btn');
            const showLessBtn = document.querySelector('.show-less-btn');

            if (descriptionContent && toggleBtnContainer) {
                // Check if content height exceeds the max-height
                if (descriptionContent.scrollHeight > descriptionContent.offsetHeight) {
                    // Content is taller than the container, show the toggle button
                    toggleBtnContainer.classList.remove('d-none');

                    showMoreBtn.addEventListener('click', function() {
                        descriptionContent.classList.add('expanded');
                        showMoreBtn.classList.add('d-none');
                        showLessBtn.classList.remove('d-none');
                    });

                    showLessBtn.addEventListener('click', function() {
                        descriptionContent.classList.remove('expanded');
                        showLessBtn.classList.add('d-none');
                        showMoreBtn.classList.remove('d-none');

                        // Scroll back to start of description
                        descriptionContent.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initDescriptionToggle();
            
            // Any other existing scripts...
        });
    </script>
@endpush
