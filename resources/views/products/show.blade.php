@extends('layouts.app')

@section('title', $product->name . ' | ورشة يحيى للموس البوسعادي')
@section('description', $product->short_desc ?: $product->name . ' - صناعة يدوية أصيلة من ورشة يحيى للموس البوسعادي.')

@section('canonical')
<link rel="canonical" href="{{ route('products.show', $product->slug) }}">
@endsection

@section('og_meta')
<meta property="og:title"       content="{{ $product->name }}">
<meta property="og:description" content="{{ $product->short_desc }}">
<meta property="og:image"       content="{{ asset('images/' . $product->image) }}">
<meta property="og:type"        content="product">
<meta property="product:price:amount"   content="{{ $product->price }}">
<meta property="product:price:currency" content="DZD">
@endsection

@section('head_extra')
<script type="application/ld+json">{!! json_encode([
    '@context'    => 'https://schema.org/',
    '@type'       => 'Product',
    'name'        => $product->name,
    'description' => $product->short_desc,
    'image'       => asset('images/' . $product->image),
    'brand'       => ['@type' => 'Brand', 'name' => 'ورشة يحيى'],
    'offers'      => [
        '@type'         => 'Offer',
        'priceCurrency' => 'DZD',
        'price'         => (string) $product->price,
        'availability'  => 'https://schema.org/InStock',
    ],
], JSON_UNESCAPED_UNICODE) !!}</script>
<style>
    .product-detail { padding: 60px 0; }
    .product-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; }
    .product-gallery-main { position: relative; border-radius: 16px; overflow: hidden; background: var(--bg-card); border: 1px solid var(--border-gold); aspect-ratio: 1; }
    .product-gallery-main img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s; }
    .product-gallery-main:hover img { transform: scale(1.03); }
    .product-thumbs { display: flex; gap: 10px; margin-top: 14px; flex-wrap: wrap; }
    .thumb { width: 72px; height: 72px; border-radius: 10px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: border-color .2s, transform .2s; flex-shrink: 0; }
    .thumb:hover { transform: scale(1.06); }
    .thumb.active { border-color: var(--gold); }
    .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .product-detail-info { display: flex; flex-direction: column; gap: 18px; }
    .product-detail-category { font-size: .8rem; font-weight: 700; color: var(--gold); text-transform: uppercase; letter-spacing: .06em; }
    .product-detail-title { font-size: 2rem; font-weight: 800; color: var(--text-light); line-height: 1.3; margin: 0; }
    .product-detail-price-row { display: flex; align-items: baseline; gap: 14px; }
    .product-detail-price { font-size: 2rem; font-weight: 800; color: var(--gold); }
    .product-detail-old-price { font-size: 1.1rem; color: var(--text-muted); text-decoration: line-through; }
    .product-detail-badge { display: inline-block; background: var(--gold); color: var(--bg-dark); font-size: .85rem; font-weight: 800; padding: 4px 14px; border-radius: 20px; }
    .product-detail-savings { font-size: .9rem; color: #27ae60; font-weight: 600; }
    .product-detail-desc { color: var(--text-muted); line-height: 1.9; }
    .product-features { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 8px; }
    .product-features li { display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-size: .95rem; }
    .product-features li::before { content: '✦'; color: var(--gold); font-size: .7rem; flex-shrink: 0; }
    .product-cod-badge { display: inline-flex; align-items: center; gap: 8px; background: rgba(39,167,69,.1); color: #27ae60; border: 1px solid rgba(39,167,69,.3); border-radius: 8px; padding: 8px 16px; font-size: .88rem; font-weight: 700; }
    .qty-selector { display: flex; align-items: center; gap: 0; border: 1px solid var(--border-gold); border-radius: 10px; overflow: hidden; width: fit-content; }
    .qty-selector button { background: var(--bg-card); border: none; color: var(--gold); font-size: 1.2rem; width: 42px; height: 42px; cursor: pointer; transition: background .2s; display: flex; align-items: center; justify-content: center; }
    .qty-selector button:hover { background: rgba(212,175,55,.1); }
    .qty-selector span { padding: 0 18px; color: var(--text-light); font-weight: 700; font-size: 1rem; min-width: 40px; text-align: center; }
    .product-actions { display: flex; flex-direction: column; gap: 12px; }
    .product-actions .btn { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 15px; font-size: 1rem; }
    .related-section { padding: 60px 0; border-top: 1px solid var(--border-gold); }
    @media (max-width: 768px) {
        .product-detail-grid { grid-template-columns: 1fr; gap: 32px; }
        .product-detail-title { font-size: 1.5rem; }
        .product-detail-price { font-size: 1.5rem; }
    }
</style>
@endsection

@section('content')

@php
    $gallery        = $product->gallery_images ?? [];
    $allImgs        = array_filter(array_merge([$product->image], $gallery));
    $galleryJsonStr = json_encode(array_values(array_map(
        function($i) { return asset('images/' . $i); },
        array_filter($allImgs)
    )));
    $waText = 'مرحبا ورشة يحيى!' . "\n"
            . 'أريد الطلب: ' . $product->name . "\n"
            . 'السعر: ' . $product->formatted_price . "\n"
            . 'الدفع عند الاستلام';
@endphp

<section class="page-hero page-hero-sm">
    <div class="page-hero-overlay"></div>
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <a href="{{ route('products.index') }}">المنتجات</a>
            <i class="fas fa-chevron-left"></i>
            <span>{{ $product->name }}</span>
        </nav>
    </div>
</section>

<section class="product-detail">
    <div class="container">
        <div class="product-detail-grid">

            {{-- Gallery column --}}
            <div>
                <div class="product-gallery-main">
                    <img src="{{ asset('images/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         id="mainProductImg">
                    @if ($product->discount_percent)
                    <span style="position:absolute;top:16px;right:16px;z-index:2;background:var(--gold);color:var(--bg-dark);font-weight:800;padding:6px 16px;border-radius:20px;font-size:1rem;">
                        -{{ $product->discount_percent }}%
                    </span>
                    @endif
                </div>

                @if (!empty($gallery))
                <div class="product-thumbs">
                    <div class="thumb active" onclick="changeMainImage(this)" tabindex="0" role="button">
                        <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}">
                    </div>
                    @foreach ($gallery as $gImg)
                    <div class="thumb" onclick="changeMainImage(this)" tabindex="0" role="button">
                        <img src="{{ asset('images/' . $gImg) }}" alt="{{ $product->name }}" loading="lazy">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Info column --}}
            <div class="product-detail-info">
                <span class="product-detail-category">{{ $product->category_label }}</span>
                <h1 class="product-detail-title">{{ $product->name }}</h1>

                <div class="product-detail-price-row">
                    <span class="product-detail-price">{{ $product->formatted_price }}</span>
                    @if ($product->old_price)
                    <span class="product-detail-old-price">{{ $product->formatted_old_price }}</span>
                    <span class="product-detail-badge">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>

                @if ($product->savings)
                <p class="product-detail-savings"><i class="fas fa-tag"></i> توفر {{ $product->savings }}</p>
                @endif

                <p class="product-detail-desc">{{ $product->description }}</p>

                @if (!empty($product->features))
                <ul class="product-features">
                    @foreach ($product->features as $feature)
                    <li>{{ $feature }}</li>
                    @endforeach
                </ul>
                @endif

                <div class="product-cod-badge">
                    <i class="fas fa-money-bill-wave"></i> الدفع عند الاستلام (COD)
                </div>

                <div class="qty-selector">
                    <button id="qtyMinus" aria-label="إنقاص الكمية">-</button>
                    <span id="qtyValue">1</span>
                    <button id="qtyPlus" aria-label="زيادة الكمية">+</button>
                </div>

                <div class="product-actions">
                    <button class="btn btn-primary btn-ripple add-to-cart-detail"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}">
                        <i class="fas fa-cart-plus"></i> أضف للسلة
                    </button>
                    <a href="https://wa.me/213775108618?text={{ rawurlencode($waText) }}"
                       target="_blank"
                       class="btn"
                       style="background:#25d366;border-color:#25d366;color:#fff;display:flex;align-items:center;justify-content:center;gap:10px;padding:15px;">
                        <i class="fab fa-whatsapp"></i> اطلب مباشرة عبر واتساب
                    </a>
                </div>

                <div style="display:flex; flex-direction:column; gap:8px; margin-top:8px;">
                    <p style="color:var(--text-muted); font-size:.88rem; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-truck" style="color:var(--gold);"></i>
                        توصيل لجميع ولايات الجزائر الـ 58
                    </p>
                    <p style="color:var(--text-muted); font-size:.88rem; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-shield-alt" style="color:var(--gold);"></i>
                        ضمان الجودة وإمكانية الإرجاع
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Related Products --}}
@if ($related->count())
<section class="related-section">
    <div class="container">
        <h2 class="section-title" style="text-align:center; margin-bottom:36px;">منتجات ذات صلة</h2>
        <div class="products-grid" style="max-width:900px; margin:0 auto;">
            @foreach ($related as $rel)
            @php
                $relGallery = $rel->gallery_images ?? [];
                $relAllImgs = array_filter(array_merge([$rel->image], $relGallery));
                $relGalleryJson = json_encode(array_values(array_map(
                    function($i) { return asset('images/' . $i); },
                    array_filter($relAllImgs)
                )));
            @endphp
            <div class="product-card"
                 data-category="{{ $rel->category }}"
                 data-id="{{ $rel->id }}"
                 data-name="{{ $rel->name }}"
                 data-price="{{ $rel->price }}"
                 data-old-price="{{ $rel->old_price ?? '' }}"
                 data-img="{{ asset('images/' . $rel->image) }}"
                 data-gallery="{{ $relGalleryJson }}"
                 data-desc="{{ $rel->short_desc ?? '' }}"
                 style="cursor:pointer;">
                <div class="product-image">
                    @if ($rel->discount_percent)
                    <span class="product-badge">-{{ $rel->discount_percent }}%</span>
                    @endif
                    <a href="{{ route('products.show', $rel->slug) }}" class="product-image-link" aria-label="{{ $rel->name }}"></a>
                    <img src="{{ asset('images/' . $rel->image) }}" alt="{{ $rel->name }}" loading="lazy">
                    <div class="product-frame"></div>
                    <div class="product-quick-view-hint"><i class="fas fa-eye"></i> عرض التفاصيل</div>
                </div>
                <div class="product-info">
                    <h3 class="product-title">
                        <a href="{{ route('products.show', $rel->slug) }}">{{ $rel->name }}</a>
                    </h3>
                    <p class="product-desc">{{ $rel->short_desc }}</p>
                    <div class="product-footer">
                        <div class="price-wrapper">
                            <span class="product-price">{{ $rel->formatted_price }}</span>
                            @if ($rel->old_price)
                            <span class="product-price-old">{{ $rel->formatted_old_price }}</span>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-ripple add-to-cart"
                                data-id="{{ $rel->id }}"
                                data-name="{{ $rel->name }}"
                                data-price="{{ $rel->price }}">
                            أضف للسلة
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
