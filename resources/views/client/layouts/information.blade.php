@extends('client.layouts.app')

@section('title')
    @yield('info_title', 'User Information')
@endsection

@section('description')
    @yield('info_description', 'Your personal information')
@endsection

@section('keywords')
    @yield('info_keyword', 'user information, account details')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/information.css') }}">
@endpush

@section('content')
    @include('components.breadcrumb', [
        'title' => 'My Account',
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'My Account', 'url' => route('user.my.account'),'active' => true],
          
        ]
    ])

    <div class="container mt-5">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-12 col-lg-3">
                <div class="user-sidebar pe-1 ">
                    <div class="user-nav">
                        <div class="user-nav-item">
                            <a href=""
                                class="user-nav-link color-primary-5 text-decoration-none {{ request()->routeIs('user.orders') ? 'active' : '' }}">
                                <img src="{{ asset('assets/images/svg/orders.svg') }}" alt="Orders"
                                    class="user-nav-icon">
                                <span class="user-nav-text">Orders</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href=""
                                class="user-nav-link color-primary-5 text-decoration-none {{ request()->routeIs('user.wishlist') ? 'active' : '' }}">
                                <i class="fa-regular fa-heart user-nav-icon"></i>
                                <span class="user-nav-text">Wishlist</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.addresses') }}"
                                class="user-nav-link color-primary-5 text-decoration-none {{ request()->routeIs('user.addresses') ? 'active' : '' }}">
                                <img src="{{ asset('assets/images/svg/express.svg') }}" alt="Address"
                                    class="user-nav-icon">
                                <span class="user-nav-text">Address</span>
                            </a>
                        </div>

                        <div class="user-nav-item">
                            <a href="{{ route('user.my.account') }}"
                                class="user-nav-link color-primary-5 text-decoration-none  {{ request()->routeIs('user.my.account') ? 'active' : '' }}">
                               <i class="fa-regular fa-user user-nav-icon"></i>
                                <span class="user-nav-text">Account Detail</span>
                            </a>
                        </div>

                        <div class="user-nav-item user-nav-logout">
                            <a href="{{ route('logout') }}" class="user-nav-link text-danger text-decoration-none">
                                <i class="fa-solid fa-arrow-right-from-bracket user-nav-icon"></i>
                                <span class="user-nav-text">Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-12 col-lg-9 border-start">
                <div class="user-content ms-3">
                    <div class="content-header">
                        <h4 class="content-title">@yield('info_section_title', 'User Information')</h4>
                        @hasSection('info_section_desc')
                            <p class="content-desc">@yield('info_section_desc')</p>
                        @endif
                    </div>

                    <div class="content-body">
                        @yield('info_content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function isMobile() {
                return window.innerWidth < 992;
            }

            function scrollToContent() {
                if (isMobile()) {
                    const hasScrolled = sessionStorage.getItem('hasScrolledToContent');

                    if (!hasScrolled) {
                        const contentOffset = $('.user-content').offset().top;

                        $('html, body').animate({
                            scrollTop: contentOffset - 20
                        }, 500);

                        sessionStorage.setItem('hasScrolledToContent', 'true');
                    }
                }
            }

            setTimeout(scrollToContent, 300);

            $('.user-nav-link').on('click', function() {
                sessionStorage.removeItem('hasScrolledToContent');
            });
        });
    </script>
    @stack('info_scripts')
@endpush