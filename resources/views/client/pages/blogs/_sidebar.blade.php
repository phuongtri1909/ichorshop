<div class="">
    <!-- Search Form -->
    <div class="mb-4">
        <h5 class="fw-bold mb-4">Search</h5>
        <form action="{{ route('blogs.index') }}" method="GET">
            <div class="input-group blog-search">
                <input type="text" class="form-control rounded-4" placeholder="Search..." name="search" value="{{ request('search') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>

    <!-- Categories -->
    <div class="mb-4">
        <h5 class="fw-bold mb-4">Categories</h5>

        @foreach ($categories as $category)
            <div class="d-flex justify-content-between align-items-center">
                <a class="text-decoration-none color-primary-5 mb-2" href="{{ route('blogs.category', $category->slug) }}">{{ $category->name }}</a>
                <span class="category-count">{{ $category->blogs_count }}</span>
            </div>
        @endforeach
    </div>

    <!-- Latest Posts -->
    <div class="mb-4">
        <h5 class="fw-bold mb-4">Latest Posts</h5>
        <div class="latest-posts">
            @foreach ($latestPosts as $post)
                <div class="d-flex mb-3">
                    <img src="{{ Storage::url($post->image) }}" class="mr-3 rounded" alt="{{ $post->title }}" height="100" width="100" style="object-fit:cover">
                    <div class="media-body ms-3">
                        <div class="d-flex">
                            @foreach ($post->categories as $category)
                                <a href="{{ route('blogs.category', $category->slug) }}" class="text-decoration-none text-sm color-primary-5">
                                    <h6 class="text-sm">{{ Str::limit($category->name, 40) }}</h6>
                                </a>
                                
                                @if (!$loop->last)
                                    <span class="text-sm color-primary-5 me-1">,</span>
                                @endif
                            @endforeach
                        </div>
                        
                        <a href="{{ route('blogs.show', $post->slug) }}" class="text-decoration-none color-primary">
                            <h6 class="fw-bold">{{ Str::limit($post->title, 40) }}</h6>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
    <style>
        .blog-search {
            position: relative;
        }

        .blog-search input {
            padding-right: 40px;
            height: 45px;
            border-radius: 4px;
        }

        .blog-search button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 45px;
            border: none;
            background: transparent;
            color: #777;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
            z-index: 10;
            outline: none;
        }

        .blog-search button:hover {
            color: #000;
        }

        .blog-search button i {
            font-size: 16px;
        }

        .blog-search button:focus {
            box-shadow: none;
            outline: none;
        }
        
        .img-blog {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .blog-meta {
            font-size: 13px;
            color: #777;
            margin-bottom: 10px;
        }
        
        .blog-meta i {
            margin-right: 4px;
        }
        
        .blog-meta span {
            margin-right: 12px;
        }
        
        .category-count {
            background-color: #f5f5f5;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            color: #666;
        }
    </style>
@endpush