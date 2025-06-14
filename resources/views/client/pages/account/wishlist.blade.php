@extends('client.layouts.information')

@section('info_title', 'Wishlist')
@section('info_description', 'Manage your wishlist items')
@section('info_keyword', 'Wishlist, Favorite Products, Saved Items')
@section('info_section_title', 'My Wishlist')
@section('info_section_desc', 'Manage your favorite products and save items for later')

@push('breadcrumb')
    @include('components.breadcrumb', [
        'title' => 'Wishlist',
        'items' => [
            ['title' => 'Home', 'url' => route('home')],
            ['title' => 'My Account', 'url' => route('user.my.account')],
            ['title' => 'Wishlist', 'url' => route('user.wishlist.index')],
        ],
    ])
@endpush

@section('info_content')
    <div class="wishlist-container">
        @if ($wishlists->count() > 0)
            @foreach ($wishlists as $item)
                <x-item-wishlist :wishlistItem="$item" />

                @if (!$loop->last)
                    <hr class="my-4">
                @endif

            @endforeach
        @else
            <div class="empty-wishlist text-center py-5">
                <i class="far fa-heart fa-3x mb-3 text-muted"></i>
                <p class="fs-4 text-muted">Your wishlist is empty</p>
                <a href="{{ route('home') }}" class="btn btn-pry mt-3">Continue Shopping</a>
            </div>
        @endif
    </div>
@endsection

@push('styles')
@endpush
