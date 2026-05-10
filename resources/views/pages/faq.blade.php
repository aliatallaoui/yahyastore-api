@extends('layouts.app')

@section('title', 'الأسئلة الشائعة | ورشة يحيى')
@section('description', 'الأسئلة الشائعة - ورشة يحيى للموس البوسعادي. إجابات على أكثر الأسئلة شيوعاً حول المنتجات والشحن والدفع.')

@section('head_extra')
<style>
    .info-page { padding: 60px 0; }
    .info-page-header { text-align: center; margin-bottom: 48px; }
    .info-page-header h1 { font-size: 2.2rem; color: var(--gold); margin-bottom: 12px; }
    .info-page-header p { color: var(--text-muted); font-size: 1.05rem; }
    .faq-category { margin-bottom: 40px; }
    .faq-category-title { color: var(--gold); font-size: 1.25rem; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-gold); display: flex; align-items: center; gap: 10px; }
    .faq-item { background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 10px; margin-bottom: 12px; overflow: hidden; }
    .faq-question { padding: 18px 22px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; color: var(--text-light); font-weight: 600; font-size: 1rem; transition: background 0.2s; }
    .faq-question:hover { background: rgba(212,175,55,0.06); }
    .faq-question i { color: var(--gold); transition: transform 0.3s; flex-shrink: 0; margin-right: 10px; }
    .faq-answer { padding: 0 22px; max-height: 0; overflow: hidden; transition: max-height 0.3s ease, padding 0.3s; }
    .faq-answer p { color: var(--text-muted); line-height: 1.9; padding-bottom: 18px; margin: 0; }
    .faq-item.open .faq-answer { max-height: 400px; padding-top: 4px; }
    .faq-item.open .faq-question i { transform: rotate(180deg); }
    .faq-cta { text-align: center; margin-top: 48px; padding: 40px; background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 12px; }
    .faq-cta h2 { color: var(--gold); margin-bottom: 12px; }
    .faq-cta p { color: var(--text-muted); margin-bottom: 24px; }
</style>
@endsection

@section('content')

<section class="page-hero page-hero-sm">
    <div class="page-hero-overlay"></div>
    <div class="container">
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">الرئيسية</a>
            <i class="fas fa-chevron-left"></i>
            <span>الأسئلة الشائعة</span>
        </nav>
    </div>
</section>

<section class="info-page">
    <div class="container">
        <div class="info-page-header">
            <h1><i class="fas fa-question-circle"></i> الأسئلة الشائعة</h1>
            <p>إجابات واضحة على أكثر الأسئلة شيوعاً</p>
        </div>

        <div class="faq-category">
            <h2 class="faq-category-title"><i class="fas fa-box-open"></i> المنتجات والجودة</h2>

            <div class="faq-item">
                <div class="faq-question">هل المنتجات مصنوعة يدوياً فعلاً؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>نعم، جميع منتجات ورشة يحيى مصنوعة يدوياً بالكامل في ورشة متخصصة ببوسعادة، الجزائر. كل قطعة تستغرق ساعات من العمل الدقيق لضمان الجودة والأصالة.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">ما هي المواد المستخدمة في الصنع؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>نستخدم أجود المواد: فولاذ مقاوم للصدأ للشفرات، وخشب الشريمة والبيقنون وأخشاب محلية نادرة للمقابض، وجلد طبيعي عالي الجودة للأغماد والتجليد.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">هل يمكن طلب قطعة مخصصة بمواصفات معينة؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>بالتأكيد! نقبل الطلبات المخصصة من حيث النقوش والأبعاد ونوع الخشب والحفر على الشفرة. تواصل معنا عبر واتساب لمناقشة تفاصيل طلبك.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">كيف أعتني بموسي البوسعادي؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>لإطالة عمر الموس: نظّف الشفرة بعد كل استخدام وجففها جيداً، ادهن الشفرة بقطرة زيت زيتون بين الحين والآخر، واحفظ الموس في غمده عند عدم الاستخدام. استخدم المبرد (المضاية) لصيانة الحدة.</p></div>
            </div>
        </div>

        <div class="faq-category">
            <h2 class="faq-category-title"><i class="fas fa-truck"></i> الشحن والتوصيل</h2>

            <div class="faq-item">
                <div class="faq-question">هل التوصيل متاح لجميع الولايات؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>نعم، نوصل لجميع ولايات الجزائر الـ 58 عبر شركات الشحن المعتمدة. مدة التوصيل تتراوح بين 24 ساعة للمدن الكبرى و5-7 أيام للمناطق النائية.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">ما هي تكلفة الشحن؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>تكلفة التوصيل للمنزل من 600 إلى 800 DZD حسب الولاية، والتوصيل لمكتب الشحن من 400 إلى 500 DZD. الطلبات فوق 30,000 DZD تستفيد من شحن مجاني.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">كيف يمكنني تتبع طلبي؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>بعد شحن طلبك ستصلك رسالة واتساب تحتوي على رقم التتبع وشركة الشحن. يمكنك أيضاً التواصل معنا مباشرة على +213775108618 للاستفسار عن حالة طلبك.</p></div>
            </div>
        </div>

        <div class="faq-category">
            <h2 class="faq-category-title"><i class="fas fa-credit-card"></i> الدفع والطلبات</h2>

            <div class="faq-item">
                <div class="faq-question">ما هي طرق الدفع المتاحة؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>الدفع عند الاستلام (COD) هو الطريقة الوحيدة المتاحة حالياً، وهو ما يضمن أمانك التام — لا تدفع إلا بعد استلام طلبك والتأكد منه.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">هل يمكنني إلغاء طلبي بعد تأكيده؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>يمكن إلغاء الطلب قبل شحنه بالتواصل معنا فوراً عبر واتساب. بعد الشحن لا يمكن الإلغاء ولكن يمكن رفض الاستلام وإعادة الطلب.</p></div>
            </div>

            <div class="faq-item">
                <div class="faq-question">كم يستغرق تأكيد الطلب؟<i class="fas fa-chevron-down"></i></div>
                <div class="faq-answer"><p>يتم التواصل معك لتأكيد الطلب هاتفياً في غضون ساعات قليلة من تقديم الطلب خلال ساعات العمل (8 صباحاً - 8 مساءً). الطلبات المقدمة ليلاً تُؤكَّد في الصباح.</p></div>
            </div>
        </div>

        <div class="faq-cta">
            <h2>لم تجد إجابة سؤالك؟</h2>
            <p>تواصل معنا مباشرة وسنرد عليك في أقرب وقت</p>
            <a href="https://wa.me/213775108618?text={{ rawurlencode('مرحبا، لدي سؤال حول...') }}"
               target="_blank"
               class="btn btn-primary btn-ripple"
               style="display:inline-flex; align-items:center; gap:8px;">
                <i class="fab fa-whatsapp"></i> اسألنا عبر واتساب
            </a>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.faq-question').forEach(q => {
        q.addEventListener('click', () => {
            const item   = q.closest('.faq-item');
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(i => i.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });
</script>
@endsection
