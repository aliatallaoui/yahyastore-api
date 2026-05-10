<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'ورشة يحيى للموس البوسعادي - صناعة يدوية تقليدية أصيلة. توصيل لجميع الولايات والدفع عند الاستلام.')">
    <title>@yield('title', 'ورشة يحيى للموس البوسعادي')</title>
    <link rel="icon" href="{{ asset('images/logo-gold.png') }}" type="image/png">
    @yield('canonical')
    @yield('og_meta')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @yield('head_extra')
</head>
<body>
    <header class="header" id="header">
        <nav class="navbar container">
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ asset('images/logo-gold.png') }}" alt="ورشة يحيى للموس البوسعادي" class="logo-img">
            </a>
            <ul class="nav-links" id="navLinks">
                <li><a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>الصفحة الرئيسية</a></li>
                <li><a href="{{ route('products.index') }}" @class(['active' => request()->routeIs('products.*')])>المنتجات</a></li>
                <li><a href="{{ route('home') }}#story">قصة الموس</a></li>
                <li><a href="{{ route('home') }}#gallery">المعرض</a></li>
                <li><a href="{{ route('home') }}#customize">التخصيص</a></li>
                <li><a href="{{ route('home') }}#contact">اتصل بنا</a></li>
            </ul>
            <div class="nav-actions">
                <button class="icon-btn cart-btn" id="cartBtn" aria-label="سلة التسوق">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount" aria-live="polite">0</span>
                </button>
                <button class="icon-btn search-btn" aria-label="بحث"><i class="fas fa-search"></i></button>
                <button class="hamburger" id="hamburger" aria-label="القائمة">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>اشترك في نشرتنا الإخبارية</h4>
                    <p>كن أول من يعلم بالعروض الحصرية والمنتجات الجديدة</p>
                    <form class="newsletter-form" id="newsletterForm">
                        @csrf
                        <input type="email" placeholder="بريدك الإلكتروني" required>
                        <button type="submit" class="btn btn-primary">اشترك</button>
                    </form>
                </div>
                <div class="footer-col">
                    <h4>الصفحات</h4>
                    <ul>
                        <li><a href="{{ route('products.index') }}">جميع المنتجات</a></li>
                        <li><a href="{{ route('home') }}#story">قصة الورشة</a></li>
                        <li><a href="{{ route('home') }}#gallery">معرض الصور</a></li>
                        <li><a href="{{ route('home') }}#customize">التخصيص</a></li>
                        <li><a href="{{ route('home') }}#contact">اتصل بنا</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>المعلومات</h4>
                    <ul>
                        <li><a href="{{ route('privacy') }}">سياسة الاستبدال والاسترجاع</a></li>
                        <li><a href="{{ route('shipping') }}">الشحن والتسليم</a></li>
                        <li><a href="{{ route('home') }}#contact">طرق الدفع</a></li>
                        <li><a href="{{ route('faq') }}">الأسئلة الشائعة</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>تواصل معنا</h4>
                    <p class="footer-phone"><i class="fas fa-phone"></i> 0775108618</p>
                    <p class="footer-whatsapp"><i class="fab fa-whatsapp"></i> +213775108618</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} | ورشة يحيى للموس البوسعادي</p>
            </div>
        </div>
    </footer>

    {{-- Cart Sidebar --}}
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>سلة التسوق</h3>
            <button class="close-cart" id="closeCart"><i class="fas fa-times"></i></button>
        </div>
        <div class="cart-items" id="cartItems">
            <p class="empty-cart">سلة التسوق فارغة</p>
        </div>
        <div class="cart-footer">
            <div class="cart-total"><span>المجموع:</span><span id="cartTotal">0 DZD</span></div>
            <button class="btn btn-primary btn-block btn-ripple" id="checkoutBtn">إتمام الشراء <i class="fas fa-arrow-left"></i></button>
        </div>
    </div>

    {{-- Search Modal --}}
    <div class="search-modal" id="searchModal">
        <div class="search-modal-content">
            <button class="close-search" id="closeSearch"><i class="fas fa-times"></i></button>
            <input type="text" placeholder="ابحث عن منتج..." class="search-input" id="searchInput">
            <div class="search-results" id="searchResults"></div>
        </div>
    </div>

    {{-- Checkout Modal --}}
    <div class="checkout-modal-overlay" id="checkoutOverlay"></div>
    <div class="checkout-modal" id="checkoutModal">
        <div class="checkout-header">
            <h2><i class="fas fa-shopping-bag"></i> إتمام الطلب</h2>
            <button class="checkout-close" id="checkoutClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="checkout-content">
            <div id="checkoutFormScreen">
                <div class="checkout-summary">
                    <h3>ملخص الطلب</h3>
                    <div class="order-items" id="checkoutOrderItems"></div>
                    <div class="summary-row"><span>المجموع الفرعي:</span><span id="checkoutSubtotal">0 DZD</span></div>
                    <div class="summary-row"><span>الشحن:</span><span id="checkoutShipping">اختر الولاية</span></div>
                    <div class="summary-row total"><span>الإجمالي:</span><span id="checkoutGrandTotal">0 DZD</span></div>
                </div>
                <form id="checkoutForm" class="checkout-form">
                    @csrf
                    <div class="form-group">
                        <label>الاسم الكامل *</label>
                        <input type="text" id="checkoutName" placeholder="أدخل اسمك الكامل">
                        <span class="error-message" id="nameError"></span>
                    </div>
                    <div class="form-group">
                        <label>رقم الهاتف *</label>
                        <input type="tel" id="checkoutPhone" placeholder="05/06/07XXXXXXXX">
                        <span class="error-message" id="phoneError"></span>
                    </div>
                    <div class="form-group">
                        <label>الولاية *</label>
                        <select id="checkoutWilaya">
                            <option value="">اختر الولاية</option>
                        </select>
                        <span class="error-message" id="wilayaError"></span>
                    </div>
                    <div class="form-group">
                        <label>العنوان الكامل *</label>
                        <textarea id="checkoutAddress" placeholder="أدخل عنوان التوصيل بالتفصيل" rows="2"></textarea>
                        <span class="error-message" id="addressError"></span>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات إضافية</label>
                        <textarea id="checkoutNotes" placeholder="أي ملاحظات خاصة بالطلب" rows="2"></textarea>
                    </div>
                    <div class="checkout-payment-info">
                        <i class="fas fa-money-bill-wave"></i><span>الدفع عند الاستلام (COD)</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-ripple">
                        <i class="fas fa-check"></i> تأكيد الطلب
                    </button>
                </form>
            </div>
            <div id="checkoutConfirmationScreen" class="checkout-confirmation-screen" style="display:none;">
                <div class="confirmation-content">
                    <div class="confirmation-icon"><i class="fas fa-check-circle"></i></div>
                    <h2>تم تأكيد طلبك بنجاح!</h2>
                    <div class="confirmation-order-number">
                        <span>رقم الطلب:</span><strong id="confirmationOrderNumber"></strong>
                    </div>
                    <div class="confirmation-summary">
                        <h3>تفاصيل الطلب</h3>
                        <div class="order-items" id="confirmationOrderItems"></div>
                        <div class="summary-row"><span>المجموع الفرعي:</span><span id="confirmationSubtotal">0 DZD</span></div>
                        <div class="summary-row"><span>الشحن:</span><span id="confirmationShipping">0 DZD</span></div>
                        <div class="summary-row total"><span>الإجمالي:</span><span id="confirmationGrandTotal">0 DZD</span></div>
                    </div>
                    <div class="confirmation-info">
                        <p><i class="fas fa-info-circle"></i> سيتم التواصل معك قريبا لتأكيد الطلب.</p>
                    </div>
                    <a href="#" id="whatsappConfirmBtn" target="_blank" class="btn btn-whatsapp btn-block">
                        <i class="fab fa-whatsapp"></i> أرسل الطلب عبر واتساب
                    </a>
                    <button type="button" class="btn btn-outline btn-block" id="backToShopBtn" style="margin-top:10px;">
                        <i class="fas fa-arrow-right"></i> العودة للتسوق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <a href="https://wa.me/213775108618?text=مرحبا%20ورشة%20يحيى" target="_blank" class="whatsapp-float" aria-label="اتصل بنا عبر واتساب">
        <i class="fab fa-whatsapp"></i>
    </a>
    <button class="back-to-top" id="backToTop" aria-label="العودة للأعلى"><i class="fas fa-arrow-up"></i></button>

    <script>
        window.APP_CONFIG = {
            cartUrl:     '{{ route('cart.index') }}',
            cartAddUrl:  '{{ route('cart.add') }}',
            cartUpdateUrl: '{{ route('cart.update') }}',
            cartRemoveUrl: '{{ route('cart.remove') }}',
            checkoutUrl: '{{ route('checkout.store') }}',
            searchUrl:   '{{ route('products.search') }}',
            csrfToken:   '{{ csrf_token() }}',
        };
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
