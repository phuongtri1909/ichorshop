 <div class="testimonial-card p-4">
     <div class="testimonial-rating mb-3">
         <span class="rating-stars text-sm" title="5 sao">
             @php
                 $rating = 4.5 ?? 0;

                 $displayRating = round($rating * 2) / 2;
             @endphp
             @for ($i = 1; $i <= 5; $i++)
                 @if ($displayRating >= $i)
                     <i class="fas fa-star cl-ffe371 "></i>
                 @elseif ($displayRating >= $i - 0.5)
                     <i class="fas fa-star-half-alt cl-ffe371 "></i>
                 @else
                     <i class="far fa-star cl-ffe371 "></i>
                 @endif
             @endfor
         </span>
     </div>
     <div class="d-flex">
         <h6 class="fs-4 fw-bold me-2">Sarah M.</h6>
         <span class="rounded-circle bg-success-custom text-white"
             style="width: 17px; height: 17px; display: inline-flex; align-items: center; justify-content: center;">
             <i class="fa-solid fa-check fa-2xs"></i>
         </span>
     </div>
     <p class="mb-0 color-primary-5">"I'm blown away by the quality and style of the clothes I received from
         Shop.co. From casual wear to elegant dresses, every piece I've bought has exceeded my
         expectations."</p>
 </div>

 @push('styles')
     <style>
         /* Testimonials */
         .testimonial-card {
             border: 1px solid rgba(0, 0, 0, 0.1);
             border-radius: 20px;
             transition: transform 0.3s ease;
         }

         .testimonial-card:hover {
             transform: translateY(-5px);
         }

         .rating-stars {
             font-size: 18px;
         }

         .testimonial-name {
             font-weight: 700;
             color: var(--primary-color);
             margin-bottom: 1rem;
         }

         .testimonial-text {
             color: var(--primary-color-2);
             line-height: 1.6;
             margin: 0;
         }
     </style>
 @endpush
