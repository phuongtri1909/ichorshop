@extends('admin.layouts.app')

@section('content')
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="logo" height="70">
                <button id="close-sidebar" class="close-sidebar d-md-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li class="{{ Route::currentRouteNamed('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Quản lý sản phẩm -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.brands.*', 'admin.categories.*', 'admin.products.*', 'admin.product-variants.*', 'admin.dress-styles.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-box"></i>
                            <span>Quản lý sản phẩm</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed('admin.categories.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.categories.index') }}">
                                    <i class="fas fa-list"></i>
                                    <span>Danh mục</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.brands.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.brands.index') }}">
                                    <i class="fas fa-tags"></i>
                                    <span>Thương hiệu</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.dress-styles.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.dress-styles.index') }}">
                                    <i class="fas fa-tshirt"></i>
                                    <span>Kiểu dáng</span>
                                </a>
                            </li>
                            <li
                                class="{{ Route::currentRouteNamed('admin.products.*', 'admin.product-variants.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-coffee"></i>
                                    <span>Sản phẩm</span>
                                </a>
                            </li>
                            {{-- <li class="{{ Route::currentRouteNamed('admin.product-variants.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.product-variants.index') }}">
                                <i class="fas fa-layer-group"></i>
                                <span>Biến thể sản phẩm</span>
                            </a>
                        </li> --}}
                        </ul>
                    </li>

                    <!-- Quản lý bán hàng -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.orders.*', 'admin.promotions.*', 'admin.coupons.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Quản lý bán hàng</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed('admin.orders.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.orders.index') }}">
                                    <i class="fas fa-shopping-bag"></i>
                                    <span>Đơn hàng</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.promotions.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.promotions.index') }}">
                                    <i class="fas fa-percent"></i>
                                    <span>Khuyến mãi</span>
                                </a>
                            </li>

                            <li class="{{ Route::currentRouteNamed('admin.coupons.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.coupons.index') }}">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span>Mã giảm giá</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Quản lý nội dung -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.blogs.*', 'admin.banners.*', 'admin.category-blogs.*','admin.feature-sections.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-newspaper"></i>
                            <span>Quản lý nội dung</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">


                            <li class="{{ Route::currentRouteNamed('admin.category-blogs.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.category-blogs.index') }}">
                                    <i class="fa-solid fa-list"></i>
                                    <span>Danh mục Blogs</span>
                                </a>
                            </li>

                            <li class="{{ Route::currentRouteNamed('admin.blogs.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.blogs.index') }}">
                                    <i class="fa-regular fa-newspaper"></i>
                                    <span>Blogs</span>
                                </a>
                            </li>
                            {{-- <li class="{{ Route::currentRouteNamed('admin.banners.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.banners.index') }}">
                                <i class="fas fa-images"></i>
                                <span>Banner</span>
                            </a>
                        </li> --}}

                            <li class="{{ Route::currentRouteNamed('admin.faqs.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.faqs.index') }}">
                                    <i class="fas fa-question-circle"></i>
                                    <span>Faqs</span>
                                </a>
                            </li>

                            <li class="{{ Route::currentRouteNamed('admin.feature-sections.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.feature-sections.index') }}">
                                    <i class="fa-regular fa-window-restore"></i>
                                    <span>Feature</span>
                                </a>
                            </li>
                            
                        </ul>
                    </li>

                    <!-- Quản lý tương tác -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.contacts.*', 'admin.reviews.*']) ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-comments"></i>
                            <span>Quản lý tương tác</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            {{-- <li class="{{ Route::currentRouteNamed('admin.contacts.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.contacts.index') }}">
                                <i class="fa-regular fa-id-badge"></i>
                                <span>Liên hệ</span>
                            </a>
                        </li> --}}

                            <li class="{{ Route::currentRouteNamed('admin.newsletter.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.newsletter.index') }}">
                                    <i class="fas fa-envelope-open-text"></i>
                                    <span>Đăng ký bản tin</span>
                                </a>
                            </li>

                            <li class="{{ Route::currentRouteNamed('admin.reviews.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.reviews.index') }}">
                                    <i class="fa-regular fa-star"></i>
                                    <span>Đánh giá</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Cấu hình hệ thống -->
                    <li
                        class="has-submenu {{ Route::currentRouteNamed(['admin.socials.*', 'admin.logo-site.*']) || request()->is('admin/users*') || request()->is('admin/settings*') || request()->is('admin/setting-order*') ? 'open' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-cogs"></i>
                            <span>Cấu hình hệ thống</span>
                            <i class="fas fa-chevron-down submenu-arrow"></i>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteNamed('admin.socials.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.socials.index') }}">
                                    <i class="fa-solid fa-globe"></i>
                                    <span>Mạng xã hội</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.logo-site.edit') ? 'active' : '' }}">
                                <a href="{{ route('admin.logo-site.edit') }}">
                                    <i class="fas fa-image"></i>
                                    <span>Logo Site</span>
                                </a>
                            </li>
                            <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                                <a href="#">
                                    <i class="fas fa-users"></i>
                                    <span>Người dùng</span>
                                </a>
                            </li>
                            <li class="{{ Route::currentRouteNamed('admin.setting.*') ? 'active' : '' }}">
                                <a href="{{ route('admin.setting.index') }}">
                                    <i class="fas fa-cog"></i>
                                    <span>Cài đặt</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="mt-4">
                        <a href="{{ route('admin.logout') }}">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Toggle sidebar button -->
        <button id="toggle-sidebar" class="toggle-sidebar-btn">
            <i class="fas fa-chevron-left"></i>
        </button>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="content">
                    <div class="container-fluid">
                        @yield('main-content')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Submenu styles */
            .has-submenu {
                position: relative;
            }

            .submenu-toggle {
                display: flex !important;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                text-decoration: none !important;
            }

            .has-submenu.open .submenu-toggle {
                color: var(--primary-color);
            }

            .submenu-arrow {
                font-size: 12px;
                transition: transform 0.3s ease;
                margin-left: auto;
            }

            .has-submenu.open .submenu-arrow {
                transform: rotate(180deg);
            }

            .submenu {
                display: none;
                background: rgba(255, 255, 255, 0.05);
                border-radius: 6px;
                margin: 8px 0;
                padding: 0;
                list-style: none;
                overflow: hidden;
                max-height: 0;
                opacity: 0;
                transition: all 0.3s ease;
            }

            .has-submenu.open .submenu {
                display: block;
                max-height: 500px;
                opacity: 1;
            }

            .submenu li {
                margin: 0;
            }

            .submenu li a {
                padding: 12px 20px 12px 60px;
                font-size: 14px;
                color: rgba(255, 255, 255, 0.8);
                border-radius: 4px;
                margin: 2px 8px;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                text-decoration: none;
            }

            .submenu li a:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                transform: translateX(5px);
            }

            .submenu li.active a {
                background: #D1A66E;
                color: #fff;
                font-weight: 500;
            }

            .submenu li a i {
                margin-right: 12px;
                width: 16px;
                text-align: center;
                font-size: 14px;
            }

            /* Main menu active state */
            .sidebar-menu>ul>li.has-submenu.open>a {
                background: rgba(255, 255, 255, 0.1);
            }

            /* Collapsed sidebar adjustments */
            .sidebar.collapsed .submenu {
                display: none !important;
            }

            .sidebar.collapsed .submenu-arrow {
                display: none;
            }

            /* Mobile responsiveness */
            @media (max-width: 768px) {
                .submenu li a {
                    padding-left: 50px;
                    font-size: 13px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize submenu state
                initializeMenuState();

                // Handle submenu toggle
                $('.submenu-toggle').click(function(e) {
                    e.preventDefault();

                    const parentLi = $(this).closest('.has-submenu');
                    const isCurrentlyOpen = parentLi.hasClass('open');
                    const menuKey = getMenuKey(parentLi);

                    // Close all other submenus
                    $('.has-submenu').not(parentLi).each(function() {
                        const $this = $(this);
                        $this.removeClass('open');
                        saveMenuState(getMenuKey($this), false);
                    });

                    // Toggle current submenu
                    if (isCurrentlyOpen) {
                        parentLi.removeClass('open');
                        saveMenuState(menuKey, false);
                    } else {
                        parentLi.addClass('open');
                        saveMenuState(menuKey, true);
                    }
                });

                // Prevent submenu links from closing the parent menu
                $('.submenu a').click(function(e) {
                    const parentSubmenu = $(this).closest('.has-submenu');
                    if (parentSubmenu.length) {
                        const menuKey = getMenuKey(parentSubmenu);
                        saveMenuState(menuKey, true);
                    }
                });

                // Initialize menu state on page load
                function initializeMenuState() {
                    $('.has-submenu').each(function() {
                        const $this = $(this);
                        const menuKey = getMenuKey($this);

                        // If server-side already marked as open (active route), save and keep it open
                        if ($this.hasClass('open')) {
                            saveMenuState(menuKey, true);
                            return;
                        }

                        // Otherwise, check localStorage for saved state
                        const savedState = getMenuState(menuKey);
                        if (savedState === 'true') {
                            $this.addClass('open');
                        } else if (savedState === 'false') {
                            $this.removeClass('open');
                        }
                        // If no saved state, keep server-side default
                    });
                }

                // Generate unique key for each menu based on its content
                function getMenuKey(menuElement) {
                    const menuText = menuElement.find('.submenu-toggle span').first().text().trim();
                    return 'menu_' + menuText.replace(/\s+/g, '_').toLowerCase();
                }

                // Save menu state to localStorage
                function saveMenuState(menuKey, isOpen) {
                    try {
                        const menuStates = JSON.parse(localStorage.getItem('adminMenuStates') || '{}');
                        menuStates[menuKey] = isOpen;
                        localStorage.setItem('adminMenuStates', JSON.stringify(menuStates));
                    } catch (e) {
                        console.log('Error saving menu state:', e);
                    }
                }

                // Get menu state from localStorage
                function getMenuState(menuKey) {
                    try {
                        const menuStates = JSON.parse(localStorage.getItem('adminMenuStates') || '{}');
                        return menuStates[menuKey];
                    } catch (e) {
                        console.log('Error getting menu state:', e);
                        return null;
                    }
                }

                // Clear all menu states (useful for debugging)
                function clearMenuStates() {
                    localStorage.removeItem('adminMenuStates');
                }

                // Existing sidebar toggle functionality
                $('#toggle-sidebar').click(function() {
                    $('#sidebar').toggleClass('collapsed');

                    const icon = $(this).find('i');
                    if ($('#sidebar').hasClass('collapsed')) {
                        icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                    } else {
                        icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                    }
                });

                // Close sidebar on mobile when clicking outside
                $(document).click(function(e) {
                    if ($(window).width() <= 768) {
                        if (!$(e.target).closest('#sidebar, #toggle-sidebar').length) {
                            $('#sidebar').removeClass('show');
                        }
                    }
                });

                // Mobile sidebar toggle
                $('#close-sidebar').click(function() {
                    $('#sidebar').removeClass('show');
                });

                // Debug function - uncomment to clear menu states if needed
                // clearMenuStates();
            });
        </script>
    @endpush
@endsection
