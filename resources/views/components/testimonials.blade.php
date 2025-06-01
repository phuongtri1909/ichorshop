<section class="py-5 bg-primary-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-baseline mb-5">
            <h3 class="text-start fs-1 fw-semibold">OUR HAPPY CUSTOMERS</h3>
            <div class="testimonials-navigation d-none d-md-block">
                <button class="testimonials-prev btn btn-link p-0 me-3 color-primary-hover">
                    <i class="fa-solid fa-arrow-left fa-xl"></i>
                </button>
                <button class="testimonials-next btn btn-link p-0 color-primary-hover">
                    <i class="fa-solid fa-arrow-right fa-xl"></i>
                </button>
            </div>
        </div>
        
        <div class="swiper testimonials-swiper">
            <div class="swiper-wrapper">
                @foreach ($testimonials as $testimonial)
                    <div class="swiper-slide">
                        <x-item_testimonial :testimonial="$testimonial" />
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination for mobile -->
            <div class="swiper-pagination d-md-none mt-4"></div>
        </div>
    </div>
</section>

<style>
.testimonials-swiper {
    overflow: hidden;
    padding-bottom: 20px;
}

.testimonials-navigation button {
    background: none;
    border: none;
    color: inherit;
    transition: opacity 0.3s ease;
}

.testimonials-navigation button:hover {
    opacity: 0.7;
}

.testimonials-navigation button:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.swiper-pagination-bullet {
    background: var(--bs-primary);
    opacity: 0.3;
}

.swiper-pagination-bullet-active {
    opacity: 1;
}

@media (max-width: 767.98px) {
    .testimonials-swiper {
        padding-bottom: 40px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testimonialsSwiper = new Swiper('.testimonials-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.testimonials-next',
            prevEl: '.testimonials-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 30,
            }
        },
        on: {
            init: function() {
                this.update();
            }
        }
    });
});
</script>
