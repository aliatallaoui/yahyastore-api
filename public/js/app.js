'use strict';

// ══════════════════════════════════════════════════════════════════════════════
//  WILAYAS  (for checkout shipping preview)
// ══════════════════════════════════════════════════════════════════════════════
const WILAYAS = [
    { code: 28, name: 'المسيلة',         zone: 1, shipping: 400 },
    { code: 17, name: 'الجلفة',           zone: 1, shipping: 400 },
    { code: 5,  name: 'باتنة',            zone: 1, shipping: 400 },
    { code: 7,  name: 'بسكرة',            zone: 1, shipping: 400 },
    { code: 34, name: 'برج بوعريريج',     zone: 1, shipping: 400 },
    { code: 3,  name: 'الأغواط',          zone: 1, shipping: 400 },
    { code: 51, name: 'أولاد جلال',       zone: 1, shipping: 400 },
    { code: 16, name: 'الجزائر',          zone: 2, shipping: 600 },
    { code: 9,  name: 'البليدة',          zone: 2, shipping: 600 },
    { code: 19, name: 'سطيف',             zone: 2, shipping: 600 },
    { code: 25, name: 'قسنطينة',          zone: 2, shipping: 600 },
    { code: 6,  name: 'بجاية',            zone: 2, shipping: 600 },
    { code: 15, name: 'تيزي وزو',         zone: 2, shipping: 600 },
    { code: 26, name: 'المدية',           zone: 2, shipping: 600 },
    { code: 35, name: 'بومرداس',          zone: 2, shipping: 600 },
    { code: 10, name: 'البويرة',          zone: 2, shipping: 600 },
    { code: 43, name: 'ميلة',             zone: 2, shipping: 600 },
    { code: 4,  name: 'أم البواقي',       zone: 2, shipping: 600 },
    { code: 40, name: 'خنشلة',            zone: 2, shipping: 600 },
    { code: 12, name: 'تبسة',             zone: 2, shipping: 600 },
    { code: 18, name: 'جيجل',             zone: 2, shipping: 600 },
    { code: 36, name: 'الطارف',           zone: 2, shipping: 600 },
    { code: 21, name: 'سكيكدة',           zone: 2, shipping: 600 },
    { code: 44, name: 'عين الدفلة',       zone: 2, shipping: 600 },
    { code: 42, name: 'تيبازة',           zone: 2, shipping: 600 },
    { code: 2,  name: 'الشلف',            zone: 2, shipping: 600 },
    { code: 27, name: 'مستغانم',          zone: 2, shipping: 600 },
    { code: 38, name: 'تيسمسيلت',         zone: 2, shipping: 600 },
    { code: 14, name: 'تيارت',            zone: 2, shipping: 600 },
    { code: 20, name: 'سعيدة',            zone: 2, shipping: 600 },
    { code: 29, name: 'معسكر',            zone: 2, shipping: 600 },
    { code: 47, name: 'غرداية',           zone: 2, shipping: 600 },
    { code: 39, name: 'الوادي',           zone: 2, shipping: 600 },
    { code: 30, name: 'ورقلة',            zone: 2, shipping: 600 },
    { code: 55, name: 'توقرت',            zone: 2, shipping: 600 },
    { code: 57, name: 'المغير',           zone: 2, shipping: 600 },
    { code: 58, name: 'المنيعة',          zone: 2, shipping: 600 },
    { code: 31, name: 'وهران',            zone: 3, shipping: 800 },
    { code: 13, name: 'تلمسان',           zone: 3, shipping: 800 },
    { code: 22, name: 'سيدي بلعباس',      zone: 3, shipping: 800 },
    { code: 46, name: 'عين تموشنت',       zone: 3, shipping: 800 },
    { code: 48, name: 'غليزان',           zone: 3, shipping: 800 },
    { code: 41, name: 'سوق أهراس',        zone: 3, shipping: 800 },
    { code: 23, name: 'عنابة',            zone: 3, shipping: 800 },
    { code: 24, name: 'قالمة',            zone: 3, shipping: 800 },
    { code: 32, name: 'البيض',            zone: 3, shipping: 800 },
    { code: 45, name: 'النعامة',          zone: 3, shipping: 800 },
    { code: 8,  name: 'بشار',             zone: 3, shipping: 800 },
    { code: 1,  name: 'أدرار',            zone: 3, shipping: 800 },
    { code: 37, name: 'تندوف',            zone: 3, shipping: 800 },
    { code: 11, name: 'تمنراست',          zone: 3, shipping: 800 },
    { code: 33, name: 'إليزي',            zone: 3, shipping: 800 },
    { code: 49, name: 'تيميمون',          zone: 3, shipping: 800 },
    { code: 50, name: 'برج باجي مختار',   zone: 3, shipping: 800 },
    { code: 52, name: 'بني عباس',         zone: 3, shipping: 800 },
    { code: 53, name: 'عين صالح',         zone: 3, shipping: 800 },
    { code: 54, name: 'عين قزام',         zone: 3, shipping: 800 },
    { code: 56, name: 'جانت',             zone: 3, shipping: 800 },
];

// ══════════════════════════════════════════════════════════════════════════════
//  HELPERS
// ══════════════════════════════════════════════════════════════════════════════
function esc(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');
}
function el(id) { return document.getElementById(id); }

function csrfToken() {
    const cfg = window.APP_CONFIG;
    if (cfg && cfg.csrfToken) return cfg.csrfToken;
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

async function apiFetch(url, method, body) {
    const opts = {
        method: method || 'GET',
        headers: {
            'Content-Type':  'application/json',
            'Accept':        'application/json',
            'X-CSRF-TOKEN':  csrfToken(),
        },
    };
    if (body !== undefined) opts.body = JSON.stringify(body);
    const res  = await fetch(url, opts);
    const json = await res.json().catch(() => ({}));
    if (!res.ok) {
        const err    = new Error(json.message || json.error || 'خطأ في الخادم');
        err.errors   = json.errors || {};
        throw err;
    }
    return json;
}

// ══════════════════════════════════════════════════════════════════════════════
//  TOAST
// ══════════════════════════════════════════════════════════════════════════════
let _toastEl = null, _toastTimer = null;

function toast(msg, type) {
    if (!_toastEl) {
        _toastEl = document.createElement('div');
        _toastEl.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);background:#1a1a1a;color:#fff;padding:13px 24px;border-radius:10px;font-size:.9rem;font-weight:600;z-index:9999;opacity:0;transition:opacity .25s,transform .25s;pointer-events:none;white-space:nowrap;';
        document.body.appendChild(_toastEl);
    }
    clearTimeout(_toastTimer);
    _toastEl.textContent = msg;
    _toastEl.style.background = type === 'error' ? '#c53030' : type === 'success' ? '#276749' : '#1a1a1a';
    _toastEl.style.opacity    = '1';
    _toastEl.style.transform  = 'translateX(-50%) translateY(0)';
    _toastTimer = setTimeout(() => {
        _toastEl.style.opacity   = '0';
        _toastEl.style.transform = 'translateX(-50%) translateY(80px)';
    }, 3000);
}

// ══════════════════════════════════════════════════════════════════════════════
//  CART  (server-side session)
// ══════════════════════════════════════════════════════════════════════════════
const Cart = {
    items:      [],
    _listeners: [],

    onChange(fn) { this._listeners.push(fn); },
    _notify()    { this._listeners.forEach(fn => fn()); },

    get subtotal() { return this.items.reduce((s, i) => s + i.price * i.qty, 0); },
    get count()    { return this.items.reduce((s, i) => s + i.qty,            0); },
    get isEmpty()  { return this.items.length === 0; },

    _sync(arr) {
        this.items = (arr || []).map(i => ({
            id:    String(i.id),
            name:  i.name,
            price: Number(i.price),
            image: i.image || '',
            qty:   Number(i.qty),
        }));
        this._notify();
    },

    async load() {
        try {
            const data = await apiFetch(window.APP_CONFIG.cartUrl);
            this._sync(Array.isArray(data) ? data : (data.cart || []));
        } catch (err) {
            console.warn('Cart load failed:', err);
        }
    },

    async add(productId, qty) {
        const data = await apiFetch(window.APP_CONFIG.cartAddUrl, 'POST', {
            product_id: Number(productId),
            qty:        qty || 1,
        });
        this._sync(data.cart || []);
    },

    async updateQty(productId, newQty) {
        const data = await apiFetch(window.APP_CONFIG.cartUpdateUrl, 'PATCH', {
            product_id: Number(productId),
            qty:        newQty,
        });
        this._sync(data.cart || []);
    },

    async remove(productId) {
        const data = await apiFetch(window.APP_CONFIG.cartRemoveUrl, 'DELETE', {
            product_id: Number(productId),
        });
        this._sync(data.cart || []);
    },

    async clear() {
        this.items = [];
        this._notify();
    },
};

// ══════════════════════════════════════════════════════════════════════════════
//  CART UI
// ══════════════════════════════════════════════════════════════════════════════
function renderCartUI() {
    const countEl = el('cartCount');
    const itemsEl = el('cartItems');
    const totalEl = el('cartTotal');

    if (countEl) countEl.textContent = Cart.count;
    if (!itemsEl) return;
    if (totalEl)  totalEl.textContent = Cart.subtotal.toLocaleString('en-US') + ' DZD';

    if (Cart.isEmpty) {
        itemsEl.innerHTML = '<p class="empty-cart">سلة التسوق فارغة</p>';
        return;
    }

    itemsEl.innerHTML = Cart.items.map(item => `
        <div class="cart-item">
            <div class="cart-item-info">
                <div class="cart-item-name">${esc(item.name)}</div>
                <div class="cart-item-price">${(item.price * item.qty).toLocaleString('en-US')} DZD</div>
                <div class="cart-item-qty">
                    <button data-action="qty" data-id="${item.id}" data-delta="-1" aria-label="إنقاص الكمية">-</button>
                    <span>${item.qty}</span>
                    <button data-action="qty" data-id="${item.id}" data-delta="1" aria-label="زيادة الكمية">+</button>
                </div>
            </div>
            <button class="remove-item" data-action="remove" data-id="${item.id}" aria-label="حذف ${esc(item.name)}">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `).join('');
}

// ══════════════════════════════════════════════════════════════════════════════
//  UI UTILITIES
// ══════════════════════════════════════════════════════════════════════════════
function initMobileMenu() {
    const hamburger = el('hamburger');
    const navLinks  = el('navLinks');
    if (!hamburger || !navLinks) return;
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('active');
    });
    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
        });
    });
}

function initHeaderScroll() {
    const header = el('header');
    if (!header) return;
    window.addEventListener('scroll', () => {
        header.style.background = window.pageYOffset > 100
            ? 'rgba(17,17,8,.98)' : 'rgba(17,17,8,.95)';
    }, { passive: true });
}

function initBackToTop() {
    const btn = el('backToTop');
    if (!btn) return;
    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.pageYOffset > 400);
    }, { passive: true });
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

function initScrollAnimations() {
    const targets = document.querySelectorAll(
        '.feature-card, .story-grid, .testimonial-card, .customize-content, .contact-form, .stat-item, .cta-content'
    );
    if (!targets.length) return;
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    targets.forEach(t => { t.classList.add('fade-in'); obs.observe(t); });
}

function initStats() {
    const section = el('stats');
    if (!section) return;
    let done = false;
    new IntersectionObserver(entries => {
        if (entries[0].isIntersecting && !done) {
            done = true;
            document.querySelectorAll('.stat-number').forEach(num => {
                const target = parseInt(num.dataset.target);
                const step   = target / 125;
                let cur = 0;
                const t = setInterval(() => {
                    cur += step;
                    if (cur >= target) { cur = target; clearInterval(t); }
                    num.textContent = Math.floor(cur).toLocaleString('en-US');
                }, 16);
            });
        }
    }, { threshold: 0.3 }).observe(section);
}

function initTestimonials() {
    const textEl = el('testimonialText');
    if (!textEl) return;

    const testimonials = [
        { text: 'طلبت الباقة الكاملة المتكاملة وكانت الجودة فوق التوقعات. صناعة يدوية حقيقية والنقوش رائعة. الموس يدوم معاك للأبد. شكرا ورشة يحيى!', name: 'عبد الرحمان - تيزي وزو', avatar: '/images/avatar-1.jpg' },
        { text: 'اشتريت قطعتين ذبيحة وسليخة وكانت النتيجة مبهرة. التوصيل سريع والتغليف فاخر. أنصح بيها بشدة لكل من يقدر الصناعة التقليدية الجزائرية.', name: 'أحمد - وهران', avatar: '/images/avatar-2.jpg' },
        { text: 'خذيت باك العائلة كهدية للوالد وفرح بيها بزاف. جودة الفولاذ ممتازة والمقبض متين. ورشة يحيى ناس محترمين في التعامل.', name: 'محمد - العاصمة', avatar: '/images/avatar-3.jpg' },
        { text: 'طلبت موس بنقش الاسم وكان العمل احترافي بزاف. من أفضل المنتجات التقليدية الجزائرية. المقبض السلكي والنقوش يخلوه تحفة فنية فريدة.', name: 'يوسف - قسنطينة', avatar: '/images/avatar-4.jpg' },
    ];

    const dots   = document.querySelectorAll('.dot');
    const nameEl = el('testimonialName');
    const avatarEl = el('testimonialAvatar');
    const card   = el('testimonialCard');
    let current  = 0;

    if (card) card.style.transition = 'opacity .3s ease, transform .3s ease';

    function show(index) {
        current = index;
        const t = testimonials[index];
        if (card) {
            card.style.opacity = '0'; card.style.transform = 'translateY(10px)';
            setTimeout(() => {
                textEl.textContent = t.text;
                if (nameEl)   nameEl.textContent = t.name;
                if (avatarEl) avatarEl.src        = t.avatar;
                card.style.opacity = '1'; card.style.transform = 'translateY(0)';
            }, 300);
        }
        dots.forEach(d => d.classList.remove('active'));
        if (dots[index]) dots[index].classList.add('active');
    }

    show(0);
    dots.forEach(dot => dot.addEventListener('click', () => show(parseInt(dot.dataset.index))));
    let interval = setInterval(() => show((current + 1) % testimonials.length), 5000);
    const slider = document.querySelector('.testimonial-slider');
    if (slider) {
        slider.addEventListener('mouseenter', () => clearInterval(interval));
        slider.addEventListener('mouseleave', () => {
            interval = setInterval(() => show((current + 1) % testimonials.length), 5000);
        });
    }
}

function initGallerySlider() {
    const track  = el('galleryTrack');
    const dotsEl = el('galleryDots');
    if (!track) return;

    const slides = Array.from(track.children);
    const total  = slides.length;
    let idx = 0, autoTimer;

    function slideW()  { return slides[0]?.offsetWidth || 0; }
    function perView() { return Math.max(1, Math.round(track.parentElement.offsetWidth / (slideW() || 1))); }
    function maxIdx()  { return Math.max(0, total - perView()); }

    function go(n) {
        idx = Math.max(0, Math.min(n, maxIdx()));
        track.style.transform = `translateX(${idx * slideW()}px)`;
        dotsEl?.querySelectorAll('.gallery-dot').forEach((d, i) => d.classList.toggle('active', i === idx));
    }

    function buildDots() {
        if (!dotsEl) return;
        dotsEl.innerHTML = '';
        for (let i = 0; i < total; i++) {
            const d = document.createElement('button');
            d.className  = 'gallery-dot' + (i === 0 ? ' active' : '');
            d.setAttribute('aria-label', `الصورة ${i + 1}`);
            d.addEventListener('click', () => { go(i); resetAuto(); });
            dotsEl.appendChild(d);
        }
    }

    function resetAuto() {
        clearInterval(autoTimer);
        autoTimer = setInterval(() => go(idx >= maxIdx() ? 0 : idx + 1), 4500);
    }

    buildDots();
    el('galleryPrev')?.addEventListener('click', () => { go(idx - 1); resetAuto(); });
    el('galleryNext')?.addEventListener('click', () => { go(idx + 1); resetAuto(); });
    track.addEventListener('mouseenter', () => clearInterval(autoTimer));
    track.addEventListener('mouseleave', resetAuto);

    let _tx = 0;
    track.addEventListener('touchstart', e => { _tx = e.changedTouches[0].clientX; }, { passive: true });
    track.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - _tx;
        if (Math.abs(dx) > 50) { go(idx + (dx < 0 ? 1 : -1)); resetAuto(); }
    }, { passive: true });
    window.addEventListener('resize', () => go(Math.min(idx, maxIdx())), { passive: true });

    resetAuto();
}

function initLightbox() {
    const lbEl = el('lightbox');
    if (!lbEl) return;

    const galleryImages = Array.from(document.querySelectorAll('.gallery-slide img, .gallery-item img'))
        .map(img => ({ src: img.src, alt: img.alt }));
    let idx = 0;

    window.openLightbox = item => {
        const img   = item.querySelector('img');
        const lbImg = el('lightboxImg');
        const found = galleryImages.findIndex(g => g.src === img.src);
        idx = found >= 0 ? found : 0;
        lbImg.src = img.src; lbImg.alt = img.alt;
        lbEl.classList.add('active');
        document.body.style.overflow = 'hidden';
    };
    window.closeLightbox = () => {
        lbEl.classList.remove('active');
        document.body.style.overflow = '';
    };
    window.navigateLightbox = dir => {
        idx = (idx + dir + galleryImages.length) % galleryImages.length;
        const lbImg = el('lightboxImg');
        lbImg.style.opacity = '0';
        setTimeout(() => { lbImg.src = galleryImages[idx].src; lbImg.alt = galleryImages[idx].alt; lbImg.style.opacity = '1'; }, 200);
    };

    lbEl.addEventListener('click', e => { if (e.target === lbEl) window.closeLightbox(); });
    document.addEventListener('keydown', e => {
        if (!lbEl.classList.contains('active')) return;
        if (e.key === 'Escape')     window.closeLightbox();
        if (e.key === 'ArrowRight') window.navigateLightbox(-1);
        if (e.key === 'ArrowLeft')  window.navigateLightbox(1);
    });

    document.querySelectorAll('.gallery-slide').forEach(slide => {
        slide.setAttribute('tabindex', '0');
        slide.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); window.openLightbox(slide); }
        });
    });
}

function initProductDetail() {
    window.changeMainImage = thumb => {
        const main  = el('mainProductImg');
        const tImg  = thumb.querySelector('img');
        if (main && tImg) { main.src = tImg.src; main.alt = tImg.alt; }
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    };
    document.querySelectorAll('.thumb').forEach(thumb => {
        thumb.setAttribute('tabindex', '0');
        thumb.setAttribute('role', 'button');
        thumb.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); window.changeMainImage(thumb); }
        });
    });

    const qtyValue = el('qtyValue');
    const qtyMinus = el('qtyMinus');
    const qtyPlus  = el('qtyPlus');
    if (qtyMinus && qtyPlus && qtyValue) {
        qtyMinus.addEventListener('click', () => { const n = parseInt(qtyValue.textContent); if (n > 1) qtyValue.textContent = n - 1; });
        qtyPlus.addEventListener('click',  () => { qtyValue.textContent = parseInt(qtyValue.textContent) + 1; });
    }
}

function initParticles() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const container = el('heroParticles');
    if (!container) return;
    for (let i = 0; i < 30; i++) {
        const p = document.createElement('div');
        p.classList.add('hero-particle');
        p.style.left              = Math.random() * 100 + '%';
        p.style.animationDelay    = Math.random() * 6 + 's';
        p.style.animationDuration = (4 + Math.random() * 4) + 's';
        p.style.width = p.style.height = (2 + Math.random() * 3) + 'px';
        container.appendChild(p);
    }
}

function initContactForm() {
    const form = el('contactForm');
    if (!form) return;
    form.addEventListener('submit', e => {
        e.preventDefault();
        const name    = el('contactName')?.value.trim()    || '';
        const phone   = el('contactPhone')?.value.trim()   || '';
        const subject = el('contactSubject')?.value.trim() || '';
        const message = el('contactMessage')?.value.trim() || '';
        let msg = 'مرحبا ورشة يحيى!\n\n';
        if (name)    msg += `الاسم: ${name}\n`;
        if (phone)   msg += `الهاتف: ${phone}\n`;
        if (subject) msg += `الموضوع: ${subject}\n\n`;
        if (message) msg += `الرسالة:\n${message}`;
        window.open(`https://wa.me/213775108618?text=${encodeURIComponent(msg)}`, '_blank');
        const btn = form.querySelector('.btn');
        const orig = btn.textContent;
        btn.textContent = 'تم الإرسال!';
        btn.style.background = '#27ae60';
        setTimeout(() => { btn.textContent = orig; btn.style.background = ''; form.reset(); }, 2000);
    });
}

function initNewsletter() {
    const form = el('newsletterForm');
    if (!form) return;
    form.addEventListener('submit', e => {
        e.preventDefault();
        const btn = form.querySelector('.btn');
        btn.textContent = 'تم! ✓';
        setTimeout(() => { btn.textContent = 'اشترك'; form.reset(); }, 2000);
    });
}

// ══════════════════════════════════════════════════════════════════════════════
//  PRODUCTS  (filter / sort / image shimmer on server-rendered cards)
// ══════════════════════════════════════════════════════════════════════════════
function initProductsPage() {
    const grid = el('productsGrid');
    if (!grid) return;

    let currentFilter = 'all';

    function applyFilterAndSort() {
        const cards = Array.from(grid.querySelectorAll('.product-card'));

        cards.forEach(card => {
            const show = currentFilter === 'all' || card.dataset.category === currentFilter;
            card.style.display = show ? '' : 'none';
        });

        const sortSelect = el('sortProducts');
        if (sortSelect) {
            const val     = sortSelect.value;
            const visible = cards.filter(c => c.style.display !== 'none');
            if (val === 'price-low')  visible.sort((a, b) => +a.dataset.price - +b.dataset.price);
            if (val === 'price-high') visible.sort((a, b) => +b.dataset.price - +a.dataset.price);
            if (val === 'discount')   visible.sort((a, b) => +b.dataset.discount - +a.dataset.discount);
            visible.forEach(c => grid.appendChild(c));
        }

        // Update tab counts
        const counts = { all: cards.length };
        cards.forEach(c => { const cat = c.dataset.category; if (cat) counts[cat] = (counts[cat] || 0) + 1; });
        document.querySelectorAll('.filter-tab').forEach(tab => {
            const span = tab.querySelector('.tab-count');
            if (span && counts[tab.dataset.filter] !== undefined) span.textContent = counts[tab.dataset.filter];
        });
    }

    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.filter-tab').forEach(t => {
                t.classList.remove('active'); t.setAttribute('aria-pressed', 'false');
            });
            tab.classList.add('active'); tab.setAttribute('aria-pressed', 'true');
            currentFilter = tab.dataset.filter;
            applyFilterAndSort();
        });
    });
    el('sortProducts')?.addEventListener('change', applyFilterAndSort);

    // Image shimmer
    grid.querySelectorAll('.product-image img').forEach(img => {
        const mark = () => img.closest('.product-image')?.classList.add('loaded');
        if (img.complete) mark(); else img.addEventListener('load', mark);
    });

    // Fade-in with IntersectionObserver
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.08 });
    grid.querySelectorAll('.product-card').forEach(card => { card.classList.add('fade-in'); obs.observe(card); });

    applyFilterAndSort();
}

// ══════════════════════════════════════════════════════════════════════════════
//  QUICK-VIEW MODAL  (opens when clicking a product card)
// ══════════════════════════════════════════════════════════════════════════════
function initQuickViewModal() {
    const catLabels = { bundle: 'باقة', single: 'قطعة فردية', accessory: 'إكسسوار' };

    // Inject styles
    const style = document.createElement('style');
    style.textContent = `
        .pm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:2000;opacity:0;pointer-events:none;transition:opacity .25s;}
        .pm-overlay.active{opacity:1;pointer-events:all;}
        .pm{position:fixed;top:50%;left:50%;transform:translate(-50%,-48%) scale(.96);background:#fff;border-radius:18px;z-index:2001;width:min(94vw,820px);max-height:92vh;overflow-y:auto;opacity:0;pointer-events:none;transition:opacity .25s,transform .25s;}
        .pm.active{opacity:1;pointer-events:all;transform:translate(-50%,-50%) scale(1);}
        .pm-close{position:absolute;top:14px;left:14px;background:rgba(0,0,0,.08);border:none;border-radius:50%;width:38px;height:38px;cursor:pointer;font-size:1rem;display:flex;align-items:center;justify-content:center;z-index:10;transition:background .15s;}
        .pm-close:hover{background:rgba(0,0,0,.18);}
        .pm-body{display:grid;grid-template-columns:1fr 1fr;}
        .pm-img{background:#f5f5f5;border-radius:18px 0 0 18px;min-height:340px;overflow:hidden;display:flex;flex-direction:column;}
        .pm-main-img{flex:1;overflow:hidden;position:relative;min-height:0;}
        .pm-main-img img{width:100%;height:100%;object-fit:cover;display:block;transition:opacity .2s;}
        .pm-thumbs{display:flex;gap:6px;padding:8px 10px;background:#ececec;overflow-x:auto;flex-shrink:0;}
        .pm-thumbs:empty{display:none;}
        .pm-thumb{width:58px;height:58px;flex-shrink:0;border-radius:7px;overflow:hidden;cursor:pointer;border:2px solid transparent;transition:border-color .15s,transform .15s;}
        .pm-thumb:hover{transform:scale(1.06);}
        .pm-thumb.active{border-color:#c8a656;}
        .pm-thumb img{width:100%;height:100%;object-fit:cover;display:block;}
        .pm-nav{position:absolute;top:50%;transform:translateY(-50%);background:rgba(0,0,0,.45);color:#fff;border:none;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:.85rem;opacity:0;transition:opacity .2s,background .15s;z-index:5;}
        .pm-nav:hover{background:rgba(0,0,0,.7);}
        .pm-main-img:hover .pm-nav{opacity:1;}
        .pm-nav-prev{right:10px;} .pm-nav-next{left:10px;}
        .pm-counter{position:absolute;bottom:8px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,.5);color:#fff;font-size:.72rem;padding:3px 9px;border-radius:10px;pointer-events:none;opacity:0;transition:opacity .2s;}
        .pm-main-img:hover .pm-counter{opacity:1;} .pm-counter.always{opacity:1;}
        .pm-info{padding:38px 28px 28px;display:flex;flex-direction:column;}
        .pm-cat{font-size:.75rem;font-weight:700;color:#c8a656;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;}
        .pm-name{font-size:1.35rem;font-weight:800;margin:0 0 14px;line-height:1.4;color:#1a1a1a;}
        .pm-desc{font-size:.88rem;color:#555;line-height:1.8;flex:1;margin-bottom:18px;}
        .pm-price-row{display:flex;align-items:baseline;gap:10px;margin-bottom:6px;}
        .pm-price{font-size:1.6rem;font-weight:800;color:#c8a656;}
        .pm-oldprice{font-size:1rem;color:#aaa;text-decoration:line-through;}
        .pm-cod{display:inline-flex;align-items:center;gap:7px;background:#f0fff4;color:#276749;border:1px solid #c6f6d5;border-radius:8px;padding:7px 14px;font-size:.82rem;font-weight:700;margin-bottom:22px;width:fit-content;}
        .pm-actions{display:flex;flex-direction:column;gap:10px;}
        .pm-actions .btn{padding:13px;font-size:.95rem;display:flex;align-items:center;justify-content:center;gap:8px;}
        .product-quick-view-hint{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.35);color:#fff;font-size:.9rem;font-weight:600;gap:8px;opacity:0;transition:opacity .2s;border-radius:inherit;}
        .product-card:hover .product-quick-view-hint{opacity:1;}
        @media(max-width:600px){.pm-body{grid-template-columns:1fr;}.pm-img{border-radius:18px 18px 0 0;min-height:240px;}.pm-thumb{width:48px;height:48px;}.pm-info{padding:24px 20px 20px;}.pm-name{font-size:1.1rem;}}
    `;
    document.head.appendChild(style);

    document.body.insertAdjacentHTML('beforeend', `
        <div class="pm-overlay" id="pmOverlay"></div>
        <div class="pm" id="pmModal" role="dialog" aria-modal="true" aria-labelledby="pmName">
            <button class="pm-close" id="pmClose" aria-label="إغلاق"><i class="fas fa-times"></i></button>
            <div class="pm-body">
                <div class="pm-img">
                    <div class="pm-main-img">
                        <img id="pmImg" src="" alt="">
                        <button class="pm-nav pm-nav-prev" id="pmNavPrev" aria-label="السابقة"><i class="fas fa-chevron-right"></i></button>
                        <button class="pm-nav pm-nav-next" id="pmNavNext" aria-label="التالية"><i class="fas fa-chevron-left"></i></button>
                        <span class="pm-counter" id="pmCounter"></span>
                    </div>
                    <div class="pm-thumbs" id="pmThumbs"></div>
                </div>
                <div class="pm-info">
                    <span class="pm-cat"  id="pmCat"></span>
                    <h2  class="pm-name" id="pmName"></h2>
                    <p   class="pm-desc" id="pmDesc"></p>
                    <div class="pm-price-row">
                        <span class="pm-price"    id="pmPrice"></span>
                        <span class="pm-oldprice" id="pmOldPrice"></span>
                    </div>
                    <div class="pm-cod"><i class="fas fa-money-bill-wave"></i> الدفع عند الاستلام (COD)</div>
                    <div class="pm-actions">
                        <button class="btn btn-primary btn-ripple add-to-cart" id="pmCart" data-id="" data-name="" data-price="">
                            <i class="fas fa-cart-plus"></i> أضف للسلة
                        </button>
                        <button class="btn" id="pmWa" style="background:#25d366;color:#fff;">
                            <i class="fab fa-whatsapp"></i> اطلب مباشرة عبر واتساب
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `);

    const overlay = el('pmOverlay');
    const modal   = el('pmModal');
    let _gallery = [], _gIdx = 0;

    function updateCounter() {
        const cEl = el('pmCounter');
        if (!cEl) return;
        if (_gallery.length > 1) { cEl.textContent = `${_gIdx + 1} / ${_gallery.length}`; cEl.classList.add('always'); }
        else { cEl.textContent = ''; cEl.classList.remove('always'); }
    }

    function goToImage(idx) {
        _gIdx = (idx + _gallery.length) % _gallery.length;
        const img = el('pmImg');
        img.style.opacity = '0';
        setTimeout(() => { img.src = _gallery[_gIdx] || ''; img.style.opacity = '1'; }, 150);
        document.querySelectorAll('#pmThumbs .pm-thumb').forEach((t, i) => {
            t.classList.toggle('active', i === _gIdx);
            if (i === _gIdx) t.scrollIntoView({ inline: 'nearest', behavior: 'smooth' });
        });
        updateCounter();
    }

    function renderThumbs(images, name) {
        const c = el('pmThumbs');
        if (!images || images.length <= 1) { c.innerHTML = ''; return; }
        c.innerHTML = images.map((src, i) =>
            `<div class="pm-thumb${i===0?' active':''}" data-index="${i}"><img src="${esc(src)}" alt="${esc(name)}" loading="lazy"></div>`
        ).join('');
        c.querySelectorAll('.pm-thumb').forEach(t => t.addEventListener('click', () => goToImage(+t.dataset.index)));
    }

    el('pmNavPrev')?.addEventListener('click', () => goToImage(_gIdx - 1));
    el('pmNavNext')?.addEventListener('click', () => goToImage(_gIdx + 1));

    const mainImgEl = document.querySelector('.pm-main-img');
    if (mainImgEl) {
        let _tx = 0;
        mainImgEl.addEventListener('touchstart', e => { _tx = e.changedTouches[0].clientX; }, { passive: true });
        mainImgEl.addEventListener('touchend',   e => {
            const dx = e.changedTouches[0].clientX - _tx;
            if (Math.abs(dx) > 40) goToImage(_gIdx + (dx > 0 ? -1 : 1));
        }, { passive: true });
    }

    function openModal(d) {
        _gallery = (d.gallery && d.gallery.length) ? d.gallery : (d.img ? [d.img] : []);
        _gIdx    = 0;
        const img = el('pmImg');
        img.src = _gallery[0] || ''; img.alt = d.name || ''; img.style.opacity = '1';
        el('pmCat').textContent     = catLabels[d.category] || d.category || '';
        el('pmName').textContent    = d.name || '';
        el('pmDesc').textContent    = d.desc || '';
        el('pmPrice').textContent   = Number(d.price).toLocaleString('en-US') + ' DZD';
        el('pmOldPrice').textContent = d.oldPrice ? Number(d.oldPrice).toLocaleString('en-US') + ' DZD' : '';
        const cartBtn = el('pmCart');
        cartBtn.dataset.id    = d.id;
        cartBtn.dataset.name  = d.name;
        cartBtn.dataset.price = d.price;
        el('pmWa').dataset.name  = d.name;
        el('pmWa').dataset.price = d.price;
        renderThumbs(_gallery, d.name);
        const navBtn = el('pmNavPrev');
        if (navBtn) { navBtn.style.display = _gallery.length > 1 ? '' : 'none'; el('pmNavNext').style.display = navBtn.style.display; }
        updateCounter();
        overlay.classList.add('active');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        overlay.classList.remove('active');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    el('pmClose').addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);

    // Open on product card click (ignore clicks on action buttons)
    document.addEventListener('click', e => {
        if (e.target.closest('.add-to-cart, .add-to-cart-detail, #pmCart, #pmWa, .quick-whatsapp')) return;
        const card = e.target.closest('.product-card');
        if (!card || !card.dataset.id) return;
        let gallery = [];
        try { gallery = JSON.parse(card.dataset.gallery || '[]'); } catch {}
        openModal({
            id:       card.dataset.id,
            name:     card.dataset.name,
            price:    card.dataset.price,
            oldPrice: card.dataset.oldPrice,
            img:      card.dataset.img,
            gallery,
            desc:     card.dataset.desc,
            category: card.dataset.category,
        });
    });

    // WhatsApp direct from modal
    document.addEventListener('click', e => {
        const btn = e.target.closest('#pmWa');
        if (!btn) return;
        const msg = `مرحبا ورشة يحيى!\nأريد الطلب: ${btn.dataset.name}\nالسعر: ${Number(btn.dataset.price).toLocaleString('en-US')} DZD\nالدفع عند الاستلام`;
        window.open(`https://wa.me/213775108618?text=${encodeURIComponent(msg)}`, '_blank');
        closeModal();
    });

    // Quick WhatsApp on cards
    document.addEventListener('click', e => {
        const btn = e.target.closest('.quick-whatsapp');
        if (!btn) return;
        const msg = `مرحبا ورشة يحيى!\nأريد الطلب: ${btn.dataset.name}\nالسعر: ${Number(btn.dataset.price).toLocaleString('en-US')} DZD\nالدفع عند الاستلام`;
        window.open(`https://wa.me/213775108618?text=${encodeURIComponent(msg)}`, '_blank');
    });

    document.addEventListener('keydown', e => {
        if (!modal?.classList.contains('active')) return;
        if (e.key === 'Escape')     closeModal();
        if (e.key === 'ArrowRight' && _gallery.length > 1) goToImage(_gIdx - 1);
        if (e.key === 'ArrowLeft'  && _gallery.length > 1) goToImage(_gIdx + 1);
    });
}

// ══════════════════════════════════════════════════════════════════════════════
//  SEARCH
// ══════════════════════════════════════════════════════════════════════════════
let _closeSearch;

function initSearch() {
    const searchModal   = el('searchModal');
    const searchInput   = el('searchInput');
    const searchResults = el('searchResults');

    _closeSearch = () => {
        searchModal?.classList.remove('active');
        if (searchInput)   searchInput.value = '';
        if (searchResults) searchResults.innerHTML = '';
    };

    document.querySelector('.search-btn')?.addEventListener('click', () => {
        searchModal?.classList.add('active');
        setTimeout(() => searchInput?.focus(), 300);
    });
    el('closeSearch')?.addEventListener('click', _closeSearch);
    searchModal?.addEventListener('click', e => { if (e.target === searchModal) _closeSearch(); });

    let _searchTimer;
    searchInput?.addEventListener('input', e => {
        clearTimeout(_searchTimer);
        const q = e.target.value.trim();
        if (!searchResults) return;
        if (q.length < 2) { searchResults.innerHTML = ''; return; }

        _searchTimer = setTimeout(async () => {
            try {
                const json    = await apiFetch(`${window.APP_CONFIG.searchUrl}?q=${encodeURIComponent(q)}`);
                const results = Array.isArray(json) ? json : (json.data || []);

                if (!results.length) {
                    searchResults.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:20px;">لا توجد نتائج</p>';
                    return;
                }

                searchResults.innerHTML = results.map(p => `
                    <div class="search-result-item" data-slug="${esc(p.slug)}" data-id="${p.id}"
                         data-name="${esc(p.name)}" data-price="${p.price}"
                         role="button" tabindex="0">
                        <span class="result-name">${esc(p.name)}</span>
                        <span class="result-price">${Number(p.price).toLocaleString('en-US')} DZD</span>
                    </div>
                `).join('');

                searchResults.querySelectorAll('.search-result-item').forEach(item => {
                    const go = () => {
                        _closeSearch();
                        if (item.dataset.slug) {
                            window.location.href = `${window.location.origin}/products/${item.dataset.slug}`;
                        }
                    };
                    item.addEventListener('click', go);
                    item.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); go(); } });
                });
            } catch {}
        }, 300);
    });
}

// ══════════════════════════════════════════════════════════════════════════════
//  CHECKOUT
// ══════════════════════════════════════════════════════════════════════════════
const Checkout = {
    _submitting: false,

    open() {
        if (Cart.isEmpty) { toast('سلة التسوق فارغة!', 'error'); return; }
        el('cartSidebar')?.classList.remove('active');
        el('cartOverlay')?.classList.remove('active');
        el('checkoutFormScreen').style.display = 'block';
        el('checkoutConfirmationScreen').style.display = 'none';
        el('checkoutOverlay').classList.add('active');
        el('checkoutModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        this.updateSummary();
    },

    close() {
        el('checkoutOverlay').classList.remove('active');
        el('checkoutModal').classList.remove('active');
        document.body.style.overflow = '';
        el('checkoutForm').reset();
        this.clearErrors();
    },

    clearErrors() {
        document.querySelectorAll('.error-message').forEach(e => { e.classList.remove('show'); e.textContent = ''; });
        document.querySelectorAll('.checkout-form input, .checkout-form select, .checkout-form textarea')
            .forEach(e => e.classList.remove('error'));
    },

    showError(fieldId, errId, msg) {
        el(fieldId)?.classList.add('error');
        const err = el(errId);
        if (err) { err.textContent = msg; err.classList.add('show'); }
    },

    calcShipping(wilayaCode) {
        const w = WILAYAS.find(x => x.code == wilayaCode);
        return w ? w.shipping : 600;
    },

    updateSummary() {
        const subtotal   = Cart.subtotal;
        const wilayaCode = el('checkoutWilaya')?.value;
        const shipping   = wilayaCode ? this.calcShipping(wilayaCode) : 0;
        const total      = subtotal + shipping;

        const itemsHtml = Cart.items.map(i => `
            <div class="order-item">
                <span class="order-item-name">${esc(i.name)} x${i.qty}</span>
                <span class="order-item-price">${(i.price * i.qty).toLocaleString('en-US')} DZD</span>
            </div>
        `).join('');

        const set     = (id, v) => { const e = el(id); if (e) e.textContent = v; };
        const setHtml = (id, v) => { const e = el(id); if (e) e.innerHTML  = v; };

        setHtml('checkoutOrderItems',   itemsHtml);
        set('checkoutSubtotal',  subtotal.toLocaleString('en-US') + ' DZD');
        set('checkoutShipping',  wilayaCode ? shipping.toLocaleString('en-US') + ' DZD' : 'اختر الولاية');
        set('checkoutGrandTotal', total.toLocaleString('en-US') + ' DZD');

        return { subtotal, shipping, total };
    },

    validate() {
        this.clearErrors();
        let ok = true;
        const name  = el('checkoutName')?.value.trim() || '';
        const phone = el('checkoutPhone')?.value.trim() || '';
        if (!name)          { this.showError('checkoutName',  'nameError',  'الاسم الكامل مطلوب');  ok = false; }
        else if (name.length < 3) { this.showError('checkoutName', 'nameError', 'الاسم قصير جداً'); ok = false; }
        if (!phone)         { this.showError('checkoutPhone', 'phoneError', 'رقم الهاتف مطلوب');    ok = false; }
        else if (!/^(05|06|07)\d{8}$/.test(phone)) { this.showError('checkoutPhone', 'phoneError', 'صيغة الرقم غير صحيحة (05/06/07 + 8 أرقام)'); ok = false; }
        if (!el('checkoutWilaya')?.value)           { this.showError('checkoutWilaya', 'wilayaError', 'الولاية مطلوبة');       ok = false; }
        const address = el('checkoutAddress')?.value.trim() || '';
        if (!address) { this.showError('checkoutAddress', 'addressError', 'العنوان مطلوب');         ok = false; }
        return ok;
    },

    initWilayaSelect() {
        const select = el('checkoutWilaya');
        if (!select) return;
        const sorted = [...WILAYAS].sort((a, b) => a.code - b.code);
        sorted.forEach(w => {
            const opt       = document.createElement('option');
            opt.value       = w.code;
            opt.textContent = `${String(w.code).padStart(2,'0')} - ${w.name}`;
            select.appendChild(opt);
        });
        select.addEventListener('change', () => {
            this.updateSummary();
            el('wilayaError')?.classList.remove('show');
        });
    },

    async handleSubmit(e) {
        e.preventDefault();
        if (this._submitting || !this.validate()) return;
        this._submitting = true;

        const submitBtn = el('checkoutForm').querySelector('[type="submit"]');
        const origHTML  = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';

        const payload = {
            name:    el('checkoutName').value.trim(),
            phone:   el('checkoutPhone').value.trim(),
            wilaya:  parseInt(el('checkoutWilaya').value),
            address: el('checkoutAddress').value.trim(),
            notes:   el('checkoutNotes')?.value.trim() || null,
        };

        try {
            const data = await apiFetch(window.APP_CONFIG.checkoutUrl, 'POST', payload);

            // Show confirmation screen
            el('confirmationOrderNumber').textContent = data.order_number || '';

            const itemsHtml = Cart.items.map(i => `
                <div class="order-item">
                    <span class="order-item-name">${esc(i.name)} x${i.qty}</span>
                    <span class="order-item-price">${(i.price * i.qty).toLocaleString('en-US')} DZD</span>
                </div>
            `).join('');
            const setHtml = (id, v) => { const e = el(id); if (e) e.innerHTML  = v; };
            const set     = (id, v) => { const e = el(id); if (e) e.textContent = v; };
            setHtml('confirmationOrderItems', itemsHtml);
            set('confirmationSubtotal',   (data.subtotal || 0).toLocaleString('en-US') + ' DZD');
            set('confirmationShipping',   (data.shipping || 0).toLocaleString('en-US') + ' DZD');
            set('confirmationGrandTotal', (data.total    || 0).toLocaleString('en-US') + ' DZD');

            const waBtn = el('whatsappConfirmBtn');
            if (waBtn && data.whatsapp_url) waBtn.href = data.whatsapp_url;

            el('checkoutFormScreen').style.display        = 'none';
            el('checkoutConfirmationScreen').style.display = 'flex';

            await Cart.clear();
        } catch (err) {
            console.error('Checkout error:', err);
            toast(err.message || 'فشل إرسال الطلب. يرجى المحاولة مرة أخرى.', 'error');
        } finally {
            submitBtn.disabled  = false;
            submitBtn.innerHTML = origHTML;
            this._submitting    = false;
        }
    },

    init() {
        this.initWilayaSelect();
        el('checkoutBtn')?.addEventListener('click', () => this.open());
        el('checkoutClose')?.addEventListener('click', () => this.close());
        el('checkoutOverlay')?.addEventListener('click', () => this.close());
        el('backToShopBtn')?.addEventListener('click', () => this.close());
        el('checkoutForm')?.addEventListener('submit', e => this.handleSubmit(e));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && el('checkoutModal')?.classList.contains('active')) this.close();
        });
    },
};

// ══════════════════════════════════════════════════════════════════════════════
//  MAIN
// ══════════════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', async () => {

    // ── Cart: load from server then render ────────────────────────────────────
    await Cart.load();
    Cart.onChange(renderCartUI);
    renderCartUI();

    // ── Cart sidebar open / close ─────────────────────────────────────────────
    const openCart  = () => { el('cartSidebar')?.classList.add('active'); el('cartOverlay')?.classList.add('active'); document.body.style.overflow = 'hidden'; };
    const closeCart = () => { el('cartSidebar')?.classList.remove('active'); el('cartOverlay')?.classList.remove('active'); document.body.style.overflow = ''; };

    el('cartBtn')?.addEventListener('click', openCart);
    el('closeCart')?.addEventListener('click', closeCart);
    el('cartOverlay')?.addEventListener('click', closeCart);

    // ── Cart item actions (qty / remove) ──────────────────────────────────────
    el('cartItems')?.addEventListener('click', async e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const id = btn.dataset.id;
        if (btn.dataset.action === 'qty') {
            const item = Cart.items.find(i => i.id === id);
            if (item) await Cart.updateQty(id, item.qty + parseInt(btn.dataset.delta));
        }
        if (btn.dataset.action === 'remove') await Cart.remove(id);
    });

    // ── Add to cart (delegated for cards, detail pages, and quick-view modal) ─
    document.addEventListener('click', async e => {
        const btn = e.target.closest('.add-to-cart, .add-to-cart-detail');
        if (!btn) return;
        const qtyEl = el('qtyValue');
        const qty   = qtyEl ? parseInt(qtyEl.textContent) || 1 : 1;
        const origHTML = btn.innerHTML;
        try {
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            await Cart.add(btn.dataset.id, qty);
            openCart();
            btn.innerHTML = '<i class="fas fa-check"></i> تمت الإضافة';
            btn.style.background = '#27ae60'; btn.style.borderColor = '#27ae60';
            setTimeout(() => { btn.innerHTML = origHTML; btn.style.background = ''; btn.style.borderColor = ''; btn.disabled = false; }, 1200);
        } catch (err) {
            btn.innerHTML = origHTML; btn.disabled = false;
            toast(err.message || 'خطأ في إضافة المنتج', 'error');
        }
    });

    // ── Checkout ──────────────────────────────────────────────────────────────
    Checkout.init();

    // ── Search ────────────────────────────────────────────────────────────────
    initSearch();

    // ── UI ────────────────────────────────────────────────────────────────────
    initMobileMenu();
    initHeaderScroll();
    initBackToTop();
    initScrollAnimations();
    initStats();
    initTestimonials();
    initGallerySlider();
    initLightbox();
    initProductDetail();
    initParticles();
    initContactForm();
    initNewsletter();

    // ── Products (filter/sort on server-rendered cards) ───────────────────────
    initProductsPage();
    initQuickViewModal();

    // ── Escape key closes open panels ─────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        closeCart();
        if (typeof _closeSearch === 'function') _closeSearch();
    });
});
