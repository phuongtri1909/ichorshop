 <!-- New Arrivals Section -->
 <section class="new-arrivals-section py-5 bg-primary-4">
     <div class="container">
         <h2 class="fw-semibold text-center">{{ $title }}</h2>
         <div class="row gx-4 gy-5 mt-4">
            @foreach ($products as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <x-item_product :product="$item" />
                </div>
            @endforeach
         </div>

         <div class="text-center mt-5">
             <a href="{{ route($routeName) }}" class="btn view-all-btn">View All</a>
         </div>
     </div>
 </section>
