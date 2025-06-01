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
                <li class="{{ Route::currentRouteNamed('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-list"></i>
                        <span>Danh mục</span>
                    </a>
                </li>
                <li class="{{ Route::currentRouteNamed('admin.products.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.products.index') }}">
                        <i class="fas fa-coffee"></i>
                        <span>Sản phẩm</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.news.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.news.index') }}">
                        <i class="fa-regular fa-newspaper"></i>
                        <span>Tin tức</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.banners.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.banners.index') }}">
                        <i class="fas fa-images"></i>
                        <span>Banner</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.franchise.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.franchise.index') }}">
                        <i class="fa-solid fa-box-open"></i>
                        <span>Nhượng quyền</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.franchise-contacts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.franchise-contacts.index') }}">
                        <i class="fa-solid fa-boxes-packing"></i>
                        <span>liên hệ nhượng quyền</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.contacts.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.contacts.index') }}">
                        <i class="fa-regular fa-id-badge"></i>
                        <span>liên hệ</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.socials.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.socials.index') }}">
                        <i class="fa-solid fa-globe"></i>
                        <span>Socials</span>
                    </a>
                </li>


                <li class="{{ Route::currentRouteNamed('admin.reviews.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews.index') }}">
                        <i class="fa-regular fa-star"></i>
                        <span>Đánh giá</span>
                    </a>
                </li>
                
                <li class="{{ Route::currentRouteNamed('admin.orders.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Đơn hàng</span>
                    </a>
                </li>

                <li class="{{ Route::currentRouteNamed('admin.logo-site.edit') ? 'active' : '' }}">
                    <a href="{{ route('admin.logo-site.edit') }}">
                        <i class="fas fa-cog"></i>
                        <span>Logo Site</span>
                    </a>
                </li>

                <li class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <a href="">
                        <i class="fas fa-users"></i>
                        <span>Người dùng</span>
                    </a>
                </li>
                <li class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                    <a href="">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
                <li class="mt-4">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Toggle sidebar button - half-circle attached to the sidebar edge -->
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
@endsection