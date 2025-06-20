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
                    'reviews' => $reviews,
                    'reviewsCount' => $reviewsCount,
                    'averageRating' => $averageRating,
                    'ratingCounts' => $ratingCounts,
                    'productId' => $product['id'], 
                ])
            </div>

            <!-- FAQs -->
            <div class="tab-pane fade" id="faqs" role="tabpanel">
                <div class="faqs-content">
                    @if (isset($faqs) && $faqs->count() > 0)
                        <div class="row" id="faqs-container">
                            @foreach ($faqs as $index => $faq)
                                <div class="col-12 col-md-6 faq-item">
                                    <h6>{{ $faq->question }}</h6>
                                    <p>{!! $faq->answer !!}</p>
                                </div>
                            @endforeach
                        </div>

                        @if (isset($totalFaqs) && $totalFaqs > $faqs->count())
                            <div class="faqs-toggle-btn mt-4 text-center" id="load-more-faqs-container">
                                <button class="btn btn-sm btn-link show-more-faqs">
                                    See More <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="empty-faqs text-center py-4">
                            <p class="text-muted">No FAQs available.</p>
                        </div>
                    @endif
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
            font-weight: 600;
        }

        .faqs-content .faq-item p {
            color: var(--primary-color-5);
            margin: 0;
        }

        /* FAQ Toggle Button */
        .faqs-toggle-btn .btn-link {
            color: var(--primary-color);
            text-decoration: none;
            padding: 5px 15px;
            border-radius: 15px;
            background-color: rgba(var(--primary-color-rgb), 0.05);
            transition: all 0.3s ease;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .faqs-toggle-btn .btn-link:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .faq-item.hidden {
            display: none;
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

        // FAQs show more/less functionality
        function initFaqsToggle() {
            const faqsContainer = document.getElementById('faqs-container');
            const loadMoreContainer = document.getElementById('load-more-faqs-container');
            let offset = {{ $faqs->count() ?? 0 }};
            const limit = 4; // Số lượng FAQ load thêm mỗi lần
            let loading = false;

            if (loadMoreContainer) {
                const showMoreBtn = loadMoreContainer.querySelector('.show-more-faqs');

                showMoreBtn.addEventListener('click', function() {
                    if (loading) return;

                    loading = true;
                    showMoreBtn.innerHTML =
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

                    // AJAX request to load more FAQs
                    fetch(`{{ route('faqs.load-more') }}?offset=${offset}&limit=${limit}`)
                        .then(response => response.json())
                        .then(data => {
                            // Add FAQs to container
                            if (data.faqs.length > 0) {
                                let faqsHtml = '';

                                data.faqs.forEach(faq => {
                                    faqsHtml += `
                                        <div class="col-12 col-md-6 faq-item">
                                            <h6>${faq.question}</h6>
                                            <p>${faq.answer}</p>
                                        </div>
                                    `;
                                });

                                faqsContainer.innerHTML += faqsHtml;
                                offset += data.count;

                                // Hide button if no more FAQs
                                if (offset >= {{ $totalFaqs ?? 0 }}) {
                                    loadMoreContainer.style.display = 'none';
                                }
                            } else {
                                loadMoreContainer.style.display = 'none';
                            }

                            loading = false;
                            showMoreBtn.innerHTML = 'See More <i class="fas fa-chevron-down"></i>';
                        })
                        .catch(error => {
                            console.error('Error loading FAQs:', error);
                            loading = false;
                            showMoreBtn.innerHTML = 'Try Again <i class="fas fa-redo"></i>';
                        });
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initDescriptionToggle();
            initFaqsToggle();
        });
    </script>
@endpush
