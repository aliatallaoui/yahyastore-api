@extends('layouts.app')

@section('title', 'جميع المنتجات | ورشة يحيى للموس البوسعادي')
@section('description', 'تسوق جميع منتجات ورشة يحيى للموس البوسعادي - باقات، قطع فردية، وإكسسوارات. توصيل لجميع ولايات الجزائر.')

@section('canonical')
<link rel="canonical" href="{{ route('products.index') }}">
@endsection

@section('content')

<section class="page-hero page-hero-sm">
    <div class="page-hero-overlay"></div>
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <span>المنتجات</span>
        </nav>
    </div>
</section>

<section class="products" style="padding-top:60px;">
    <div class="container">
        <h1 class="section-title" style="text-align:center; margin-bottom:32px;">جميع المنتجات</h1>

        <div class="products-toolbar">
            <div class="filter-tabs" role="group" aria-label="تصفية المنتجات">
                @foreach ($categories as $key => $cat)
                <button class="filter-tab {{ $key === 'all' ? 'active' : '' }}"
                        data-filter="{{ $key }}"
                        aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}">
                    {{ $cat['label'] }}
                    <span class="tab-count">{{ $cat['count'] }}</span>
                </button>
                @endforeach
            </div>
            <div class="products-sort">
                <select id="sortProducts" aria-label="ترتيب المنتجات">
                    <option value="">ترتيب افتراضي</option>
                    <option value="price-low">السعر: من الأقل للأعلى</option>
                    <option value="price-high">السعر: من الأعلى للأقل</option>
                    <option value="discount">الأكثر تخفيضاً</option>
                </select>
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            @foreach ($products as $product)
            @php
                $gallery = $product->gallery_images ?? [];
                $allImgs = array_filter(array_merge([$product->image], $gallery));
            @endphp
            <div class="product-card"
                 data-category="{{ $product->category }}"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ $product->price }}"
                 data-discount="{{ $product->discount_percent }}"
                 data-old-price="{{ $product->old_price ?? '' }}"
                 data-img="{{ asset('images/' . $product->image) }}"
                 data-gallery="{{ json_encode(array_values(array_map(fn($i) => asset('images/' . $i), array_filter($allImgs)))) }}"
                 data-desc="{{ $product->short_desc ?? '' }}"
                 style="cursor:pointer;">
                <div class="product-image">
                    @if ($product->discount_percent)
                        <span class="product-badge">-{{ $product->discount_percent }}%</span>
                    @endif
                    <a href="{{ route('products.show', $product->slug) }}" class="product-image-link" aria-label="{{ $product->name }}"></a>
                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                    <div class="product-frame"></div>
                    <div class="product-quick-actions">
                        <button class="quick-add add-to-cart"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                title="أضف للسلة">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                        <button class="quick-whatsapp"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                title="اطلب عبر واتساب">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>
                <div class="product-info">
                    <span class="product-category-tag">{{ $product->category_label }}</span>
                    <h3 class="product-title">
                        <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="product-desc">{{ $product->short_desc }}</p>
                    <div class="product-footer">
                        <div class="price-wrapper">
                            <span class="product-price">{{ $product->formatted_price }}</span>
                            @if ($product->old_price)
                                <span class="product-price-old">{{ $product->formatted_old_price }}</span>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-ripple add-to-cart"
                                data-id="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}">
                            أضف للسلة
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if ($products->isEmpty())
        <div style="text-align:center; padding:80px 20px; color:var(--text-muted);">
            <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:16px; display:block; color:var(--gold);"></i>
            <p>لا توجد منتجات متاحة حالياً.</p>
        </div>
        @endif
    </div>
</section>

@endsection
