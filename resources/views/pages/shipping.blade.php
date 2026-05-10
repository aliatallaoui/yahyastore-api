@extends('layouts.app')

@section('title', 'الشحن والتسليم | ورشة يحيى')
@section('description', 'معلومات الشحن والتسليم - ورشة يحيى للموس البوسعادي. توصيل لجميع ولايات الجزائر مع الدفع عند الاستلام.')

@section('head_extra')
<style>
    .info-page { padding: 60px 0; }
    .info-page-header { text-align: center; margin-bottom: 48px; }
    .info-page-header h1 { font-size: 2.2rem; color: var(--gold); margin-bottom: 12px; }
    .info-page-header p { color: var(--text-muted); font-size: 1.05rem; }
    .info-card { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 12px; padding: 36px; margin-bottom: 28px; }
    .info-card h2 { color: var(--gold); font-size: 1.3rem; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
    .info-card h2 i { font-size: 1.1rem; }
    .info-card p, .info-card li { color: var(--text-muted); line-height: 1.9; margin-bottom: 10px; }
    .info-card ul { padding-right: 20px; }
    .info-card ul li { list-style: disc; }
    .info-highlight { background: rgba(212,175,55,0.08); border-right: 4px solid var(--gold); padding: 16px 20px; border-radius: 0 8px 8px 0; margin: 20px 0; color: var(--text-light); }
    .shipping-steps { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
    .shipping-step { text-align: center; padding: 24px 16px; background: var(--bg-dark); border-radius: 10px; border: 1px solid var(--border-gold); }
    .shipping-step .step-icon { font-size: 2rem; color: var(--gold); margin-bottom: 12px; }
    .shipping-step h3 { color: var(--text-light); font-size: 1rem; margin-bottom: 8px; }
    .shipping-step p { color: var(--text-muted); font-size: 0.9rem; margin: 0; }
</style>
@endsection

@section('content')

<section class="page-hero page-hero-sm">
    <div class="page-hero-overlay"></div>
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <span>الشحن والتسليم</span>
        </nav>
    </div>
</section>

<section class="info-page">
    <div class="container">
        <div class="info-page-header">
            <h1><i class="fas fa-truck"></i> الشحن والتسليم</h1>
            <p>نوصل طلبك بأمان لجميع ولايات الجزائر الـ 58</p>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-map-marker-alt"></i> التغطية الجغرافية</h2>
            <p>نوفر خدمة التوصيل لجميع ولايات الجزائر الـ 58 دون استثناء عبر شركات الشحن المعتمدة.</p>
            <div class="info-highlight">التوصيل إلى المنزل أو إلى مكتب الشحن — حسب اختيارك عند تأكيد الطلب.</div>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-clock"></i> مدة التوصيل</h2>
            <ul>
                <li><strong>الجزائر العاصمة والمدن الكبرى:</strong> من 24 إلى 48 ساعة</li>
                <li><strong>باقي الولايات:</strong> من 2 إلى 5 أيام عمل</li>
                <li><strong>المناطق النائية:</strong> من 5 إلى 7 أيام عمل</li>
            </ul>
            <p>تُحتسب مدة التوصيل من تاريخ تأكيد الطلب وليس من تاريخ الطلب.</p>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-money-bill-wave"></i> تكاليف الشحن</h2>
            <ul>
                <li>التوصيل إلى المنزل: <strong>600 إلى 800 DZD</strong> حسب الولاية</li>
                <li>التوصيل إلى مكتب الشحن: <strong>400 إلى 500 DZD</strong> حسب الولاية</li>
                <li>الطلبات فوق <strong>30,000 DZD</strong>: الشحن مجاني</li>
            </ul>
            <div class="info-highlight">الدفع عند الاستلام (COD) متاح لجميع الولايات — لا يلزمك الدفع المسبق.</div>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-list-ol"></i> خطوات الطلب والتوصيل</h2>
            <div class="shipping-steps">
                <div class="shipping-step">
                    <div class="step-icon"><i class="fas fa-cart-plus"></i></div>
                    <h3>أضف للسلة</h3>
                    <p>اختر منتجاتك وأضفها لسلة التسوق</p>
                </div>
                <div class="shipping-step">
                    <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>أكّد طلبك</h3>
                    <p>أدخل بياناتك وعنوان التوصيل</p>
                </div>
                <div class="shipping-step">
                    <div class="step-icon"><i class="fas fa-phone"></i></div>
                    <h3>تأكيد هاتفي</h3>
                    <p>سنتصل بك لتأكيد الطلب</p>
                </div>
                <div class="shipping-step">
                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                    <h3>الشحن والتسليم</h3>
                    <p>تصلك طلبيتك في المدة المحددة</p>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-question-circle"></i> تتبع طلبك</h2>
            <p>بعد شحن طلبك ستصلك رسالة واتساب تحتوي على رقم التتبع وشركة الشحن. يمكنك التواصل معنا في أي وقت للاستفسار عن حالة طلبك.</p>
            <div style="text-align:center; margin-top: 20px;">
                <a href="https://wa.me/213775108618?text={{ rawurlencode('مرحبا، أريد الاستفسار عن طلبي') }}"
                   target="_blank"
                   class="btn btn-primary btn-ripple"
                   style="display:inline-flex; align-items:center; gap:8px;">
                    <i class="fab fa-whatsapp"></i> تواصل معنا
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
