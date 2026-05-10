@extends('layouts.app')

@section('title', 'ورشة يحيى للموس البوسعادي | كل ماهو عصري تقليدي وأصلي')
@section('description', 'ورشة يحيى للموس البوسعادي - صناعة يدوية 100% من الفولاذ المقاوم للصدأ. كل ماهو عصري تقليدي وأصلي. توصيل لجميع الولايات.')

@section('canonical')
<link rel="canonical" href="{{ url('/') }}">
@endsection

@section('og_meta')
<meta property="og:title"       content="ورشة يحيى للموس البوسعادي">
<meta property="og:description" content="صناعة يدوية 100% من الفولاذ المقاوم للصدأ. توصيل لجميع الولايات والدفع عند الاستلام.">
<meta property="og:image"       content="{{ asset('images/hero-bg.jpg') }}">
<meta property="og:type"        content="website">
<meta name="twitter:card"       content="summary_large_image">
@endsection

@section('head_extra')
<link rel="preload" as="image" href="{{ asset('images/hero-bg.jpg') }}">
@endsection

@section('content')

{{-- Hero --}}
<section class="hero" id="home">
    <div class="hero-particles" id="heroParticles"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content container">
        <p class="hero-subtitle animate-text">ورشة يحيى للموس البوسعادي</p>
        <h1 class="hero-title animate-text">كل ماهو عصري تقليدي وأصلي:<br>الموس البوسعادي</h1>
        <a href="#collections" class="btn btn-primary btn-ripple animate-text">تسوق الآن</a>
    </div>
</section>

{{-- Features --}}
<section class="features" id="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="{{ asset('images/feature-handle.jpg') }}" alt="مقبض سلكي فريد"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-hand-fist\'></i>'">
                </div>
                <h3>مقبض سلكي فريد</h3>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="{{ asset('images/feature-engrave.jpg') }}" alt="نقوش عريقة"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-pen-nib\'></i>'">
                </div>
                <h3>نقوش عريقة</h3>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <img src="{{ asset('images/feature-handcraft.jpg') }}" alt="صياغة يدوية"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-hands\'></i>'">
                </div>
                <h3>صياغة يدوية</h3>
            </div>
        </div>
    </div>
</section>

{{-- Products --}}
<section class="products" id="collections">
    <div class="container">
        <h2 class="section-title">منتجاتنا المميزة</h2>

        <div class="filter-tabs" id="filterTabs" role="group" aria-label="تصفية المنتجات">
            <button class="filter-tab active" data-filter="all"       aria-pressed="true">الكل</button>
            <button class="filter-tab"        data-filter="bundle"    aria-pressed="false">باقات</button>
            <button class="filter-tab"        data-filter="single"    aria-pressed="false">قطع فردية</button>
            <button class="filter-tab"        data-filter="accessory" aria-pressed="false">إكسسوارات</button>
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
                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                    <div class="product-frame"></div>
                    <div class="product-quick-view-hint"><i class="fas fa-eye"></i> عرض التفاصيل</div>
                </div>
                <div class="product-info">
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

        <div style="text-align:center; margin-top:40px;">
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-ripple">
                <i class="fas fa-th"></i> عرض جميع المنتجات
            </a>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="stats" id="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-fire"></i></div>
                <span class="stat-number" data-target="15">0</span>
                <span class="stat-label">سنة من الإتقان</span>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-box-open"></i></div>
                <span class="stat-number" data-target="2500">0</span>
                <span class="stat-suffix">+</span>
                <span class="stat-label">قطعة مباعة</span>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-smile"></i></div>
                <span class="stat-number" data-target="1800">0</span>
                <span class="stat-suffix">+</span>
                <span class="stat-label">عميل سعيد</span>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-truck"></i></div>
                <span class="stat-number" data-target="58">0</span>
                <span class="stat-label">ولاية يصلها التوصيل</span>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="testimonials" id="testimonials">
    <div class="container">
        <div class="testimonial-slider">
            <div class="testimonial-card" id="testimonialCard">
                <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
                <p class="testimonial-text" id="testimonialText"></p>
                <div class="testimonial-author">
                    <div class="author-avatar">
                        <img src="{{ asset('images/avatar-1.jpg') }}" alt="عميل" id="testimonialAvatar"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22%23c8a656%22/%3E%3Ctext fill=%22%23fff%22 font-size=%2240%22 x=%2250%22 y=%2265%22 text-anchor=%22middle%22%3E%D8%B9%3C/text%3E%3C/svg%3E'">
                    </div>
                    <span class="author-name" id="testimonialName"></span>
                </div>
            </div>
            <div class="testimonial-dots">
                <span class="dot active" data-index="0"></span>
                <span class="dot" data-index="1"></span>
                <span class="dot" data-index="2"></span>
                <span class="dot" data-index="3"></span>
            </div>
        </div>
    </div>
</section>

{{-- Story --}}
<section class="story" id="story">
    <div class="container">
        <div class="story-grid">
            <div class="story-image parallax-img">
                <img src="{{ asset('images/story.jpg') }}" alt="قصة الموس البوسعادي"
                     onerror="this.parentElement.classList.add('placeholder')">
            </div>
            <div class="story-content">
                <h2 class="section-title">ورشة يحيى للموس البوسعادي</h2>
                <p>ورشة يحيى هي ورشة حرفية متخصصة في صناعة الموس البوسعادي الأصيل. نحافظ على تراث الصناعة التقليدية الجزائرية التي توارثها الحرفيون أبا عن جد عبر قرون.</p>
                <p>كل قطعة مصنوعة يدويا 100% من الفولاذ المقاوم للصدأ، مع عناية فائقة بالنقوش والتوازن والمقبض والغمد. موس يدوم ليك ولأحفادك وأحفاد أحفادك.</p>
                <p>نوفر إمكانية التخصيص بنقش الاسم أو التوقيع الشخصي على كل قطعة.</p>
                <a href="#customize" class="btn btn-outline btn-ripple">اطلب تخصيصك</a>
            </div>
        </div>
    </div>
</section>

{{-- Gallery --}}
<section class="gallery" id="gallery">
    <div class="container">
        <h2 class="section-title" style="text-align:center; margin-bottom:12px;">معرض الصور</h2>
        <p class="gallery-subtitle">لمحة عن إبداعاتنا وأعمالنا اليدوية</p>
        <div class="gallery-slider" id="gallerySlider">
            <div class="gallery-track" id="galleryTrack">
                @foreach (['gallery-1.jpg' => 'الباقة الكاملة في علبة فاخرة', 'gallery-2.jpg' => 'قطعتين ذبيحة وسليخة', 'gallery-3.jpg' => 'الأغماد الجلدية المنقوشة', 'gallery-4.jpg' => 'سيف بوسعادي في علبة عرض', 'gallery-5.jpg' => 'تفاصيل المقبض السلكي', 'gallery-6.jpg' => 'النقوش على النصل', 'gallery-7.jpg' => 'طقم كامل في علبة خشبية', 'gallery-8.jpg' => 'ورشة يحيى من الداخل', 'gallery-9.jpg' => 'موس بوسعادي مع غمد جلدي', 'gallery-10.jpg' => 'علبة خشبية مع بطاقة شكر', 'gallery-11.jpg' => 'موس بوسعادي في الطبيعة', 'gallery-12.jpg' => 'أغماد جلدية مزخرفة'] as $file => $alt)
                <div class="gallery-slide" onclick="openLightbox(this)">
                    <img src="{{ asset('images/' . $file) }}" alt="{{ $alt }}" loading="lazy">
                    <div class="gallery-overlay"><i class="fas fa-search-plus"></i></div>
                </div>
                @endforeach
            </div>
            <button class="gallery-arrow gallery-arrow-prev" id="galleryPrev" aria-label="السابق">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="gallery-arrow gallery-arrow-next" id="galleryNext" aria-label="التالي">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        <div class="gallery-dots" id="galleryDots"></div>
    </div>
</section>

{{-- Lightbox --}}
<div class="lightbox" id="lightbox" role="dialog" aria-label="معرض الصور">
    <button class="lightbox-close" onclick="closeLightbox()" aria-label="إغلاق المعرض"><i class="fas fa-times"></i></button>
    <button class="lightbox-prev" onclick="navigateLightbox(-1)" aria-label="الصورة السابقة"><i class="fas fa-chevron-right"></i></button>
    <button class="lightbox-next" onclick="navigateLightbox(1)"  aria-label="الصورة التالية"><i class="fas fa-chevron-left"></i></button>
    <img src="" alt="" id="lightboxImg">
</div>

{{-- Customize --}}
<section class="customize-section" id="customize">
    <div class="container">
        <div class="customize-content">
            <h2 class="section-title">خصص الموس الخاص بك</h2>
            <p>اختر نوع النصل، المقبض، والنقوش التي تريدها. نقدم خدمة نقش الاسم أو التوقيع الشخصي على كل قطعة. تواصل معنا عبر الواتساب أو النموذج أدناه.</p>
            <a href="#contact" class="btn btn-primary btn-ripple">اطلب تخصيصك الآن</a>
        </div>
    </div>
</section>

{{-- Contact --}}
<section class="contact" id="contact">
    <div class="container">
        <h2 class="section-title">اتصل بنا</h2>
        <form class="contact-form" id="contactForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="contactName">الاسم الكامل</label>
                    <input type="text" id="contactName" name="name" placeholder="الاسم الكامل" required>
                </div>
                <div class="form-group">
                    <label for="contactPhone">رقم الهاتف</label>
                    <input type="tel" id="contactPhone" name="phone" placeholder="رقم الهاتف">
                </div>
            </div>
            <div class="form-group">
                <label for="contactSubject">الموضوع</label>
                <input type="text" id="contactSubject" name="subject" placeholder="الموضوع">
            </div>
            <div class="form-group">
                <label for="contactMessage">رسالتك</label>
                <textarea id="contactMessage" name="message" placeholder="رسالتك" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-ripple">إرسال</button>
        </form>
    </div>
</section>

@endsection
