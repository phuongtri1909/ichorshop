<section class="product-tabs-section py-5">
    <div class="container">
        <!-- Tab Navigation -->
        <div class="tab-navigation">
            <ul class="nav nav-tabs border-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#details" type="button"
                        role="tab">
                        Product Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews" type="button"
                        role="tab">
                        Rating & Reviews
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#faqs" type="button" role="tab">
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
                    <h5>Product Information</h5>
                    <p>This graphic t-shirt is perfect for any occasion. Made from premium cotton blend fabric that
                        provides exceptional comfort and durability.</p>

                    <div class="details-grid">
                        <div class="detail-item">
                            <strong>Material:</strong>
                            <span>100% Cotton</span>
                        </div>
                        <div class="detail-item">
                            <strong>Fit:</strong>
                            <span>Regular Fit</span>
                        </div>
                        <div class="detail-item">
                            <strong>Care Instructions:</strong>
                            <span>Machine wash cold, tumble dry low</span>
                        </div>
                        <div class="detail-item">
                            <strong>Origin:</strong>
                            <span>Made in USA</span>
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
