@extends('client.layouts.app')
@section('title', 'Blog - Ichor Shop')
@section('description', 'Explore the latest articles on fashion, style and stay up to date on new trends.')
@section('keywords', 'blog, fashion, style, articles, tips, trends')

@push('styles')
@endpush

@section('content')
    <!-- Breadcrumb Section -->
    <x-breadcrumb :items="[['title' => 'Home', 'url' => route('home')], ['title' => 'Blog', 'url' => '']]" title="Blog" />

    <!-- Blog Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-3">
                    @include('client.pages.blogs._sidebar')
                </div>
                <!-- Blog Content -->
                <div class="col-9">
                    <div class="row">
                        @forelse ($blogs as $blog)
                            <div class="col-md-6 mb-4">
                                <div class="">
                                    <img src="{{ Storage::url($blog->image) }}" class="img-blog" alt="{{ $blog->title }}">
                                    <div class="">
                                        <div class="d-flex align-items-center my-3">
                                            @foreach ($blog->categories as $category)
                                                <a href="{{ route('blogs.category', $category->slug) }}"
                                                    class="fs-6 badge rounded-pill text-bg-light me-2 text-decoration-none">{{ $category->name }}</a>
                                            @endforeach
                                        </div>
                                        <h5 class="card-title mb-3">
                                            <a href="{{ route('blogs.show', $blog->slug) }}"
                                                class="text-decoration-none text-dark fw-bold">{{ $blog->title }}</a>
                                        </h5>
                                        <div class="blog-meta">
                                            <span class="me-0">{{ $blog->created_at->format('F d, Y') }}</span>
                                            -
                                            <span>By <span
                                                    class="fw-semibold color-primary">{{ $blog->author->full_name }}</span></span>
                                        </div>
                                        <p class="card-text">{!! Str::limit(strip_tags($blog->content), 200) !!}</p>

                                        <a href="{{ route('blogs.show', $blog->slug) }}"
                                            class="btn btn-pry rounded-3 mt-3 px-3">Read
                                            More</a>
                                    </div>

                                    <hr class="mt-3">

                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No blog posts found.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $blogs->links('components.paginate') }}
                    </div>
                </div>

                <!-- Sidebar -->

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bạn có thể thêm các script xử lý giao diện tại đây
        });
    </script>
@endpush
