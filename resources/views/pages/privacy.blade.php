@extends('layouts.app')

@section('title', 'سياسة الاستبدال والاسترجاع | ورشة يحيى')
@section('description', 'سياسة الاستبدال والاسترجاع - ورشة يحيى للموس البوسعادي. تعرف على شروط الاستبدال والإرجاع والضمان.')

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
</style>
@endsection

@section('content')

<section class="page-hero page-hero-sm">
    <div class="page-hero-overlay"></div>
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <span>سياسة الاستبدال والاسترجاع</span>
        </nav>
    </div>
</section>

<section class="info-page">
    <div class="container">
        <div class="info-page-header">
            <h1><i class="fas fa-exchange-alt"></i> سياسة الاستبدال والاسترجاع</h1>
            <p>نلتزم بتقديم أفضل تجربة تسوق — رضاك هو أولويتنا</p>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-check-circle"></i> شروط الاسترجاع</h2>
            <p>يحق للعميل إرجاع المنتج واسترداد المبلغ كاملاً في الحالات التالية:</p>
            <ul>
                <li>وصول المنتج تالفاً أو به عيب مصنعي</li>
                <li>استلام منتج مختلف عن المطلوب</li>
                <li>عدم مطابقة المنتج للمواصفات المُعلنة</li>
            </ul>
            <div class="info-highlight">يجب الإبلاغ عن أي مشكلة خلال <strong>48 ساعة</strong> من تاريخ الاستلام مع إرفاق صور للمنتج.</div>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-sync-alt"></i> شروط الاستبدال</h2>
            <p>يمكن استبدال المنتج خلال <strong>7 أيام</strong> من تاريخ الاستلام بشرط:</p>
            <ul>
                <li>أن يكون المنتج في حالته الأصلية غير مستعمل</li>
                <li>الاحتفاظ بالتغليف والملحقات الأصلية</li>
                <li>تقديم إثبات الشراء (رقم الطلب)</li>
            </ul>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-times-circle"></i> حالات لا تقبل الإرجاع</h2>
            <ul>
                <li>المنتجات المخصصة أو المُنجزة بناءً على طلب خاص</li>
                <li>المنتجات التي تم استخدامها أو تركيبها</li>
                <li>طلب الإرجاع بعد مرور 7 أيام من الاستلام</li>
                <li>التلف الناتج عن سوء الاستخدام</li>
            </ul>
        </div>

        <div class="info-card">
            <h2><i class="fas fa-phone-alt"></i> كيفية طلب الاسترجاع أو الاستبدال</h2>
            <p>للبدء في عملية الاسترجاع أو الاستبدال، تواصل معنا عبر:</p>
            <ul>
                <li>واتساب: <strong>+213775108618</strong></li>
                <li>أرسل رقم طلبك وصورة المنتج</li>
                <li>سيتم الرد خلال 24 ساعة عمل</li>
            </ul>
            <div class="info-highlight">تكاليف الشحن عند الإرجاع بسبب عيب مصنعي تتحملها ورشة يحيى كاملاً.</div>
        </div>

        <div style="text-align:center; margin-top: 40px;">
            <a href="https://wa.me/213775108618?text={{ rawurlencode('مرحبا، أريد الاستفسار عن سياسة الاسترجاع') }}"
               target="_blank"
               class="btn btn-primary btn-ripple"
               style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fab fa-whatsapp"></i> تواصل معنا عبر واتساب
            </a>
        </div>
    </div>
</section>

@endsection
