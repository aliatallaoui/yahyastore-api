<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | ورشة يحيى</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --gold: #d4af37;
            --gold-light: #f0d060;
            --bg: #0f0f0a;
            --bg-card: #1a1a10;
            --bg-sidebar: #111108;
            --border: rgba(212,175,55,.2);
            --text: #e8e8d8;
            --text-muted: #888;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #3498db;
            --radius: 10px;
        }
        body { font-family: 'Cairo', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; }

        /* Sidebar */
        .sidebar { width: 240px; background: var(--bg-sidebar); border-left: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; position: sticky; top: 0; height: 100vh; }
        .sidebar-logo { padding: 24px 20px; border-bottom: 1px solid var(--border); }
        .sidebar-logo h2 { font-size: 1rem; color: var(--gold); font-weight: 800; }
        .sidebar-logo p { font-size: .75rem; color: var(--text-muted); margin-top: 4px; }
        .sidebar-nav { flex: 1; padding: 16px 12px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 8px; color: var(--text-muted); text-decoration: none; font-size: .9rem; font-weight: 600; transition: background .15s, color .15s; margin-bottom: 4px; }
        .nav-link i { width: 18px; text-align: center; }
        .nav-link:hover, .nav-link.active { background: rgba(212,175,55,.1); color: var(--gold); }
        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border); }
        .logout-btn { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 8px; background: transparent; border: 1px solid var(--border); color: var(--text-muted); font-size: .85rem; cursor: pointer; width: 100%; font-family: inherit; font-weight: 600; transition: background .15s, color .15s; }
        .logout-btn:hover { background: rgba(231,76,60,.1); color: var(--danger); border-color: var(--danger); }

        /* Main */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { padding: 16px 28px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--bg-card); }
        .topbar-title { font-size: 1.15rem; font-weight: 800; color: var(--text); }
        .topbar-sub { font-size: .8rem; color: var(--text-muted); }
        .content { padding: 28px; flex: 1; }

        /* Alert */
        .alert { padding: 12px 18px; border-radius: var(--radius); margin-bottom: 20px; font-size: .9rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: rgba(39,174,96,.1); border: 1px solid rgba(39,174,96,.3); color: #2ecc71; }
        .alert-error   { background: rgba(231,76,60,.1);  border: 1px solid rgba(231,76,60,.3);  color: #e74c3c; }

        /* Cards / tables */
        .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); }
        .card-header { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .card-header h3 { font-size: 1rem; color: var(--text); }
        .card-body { padding: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: .88rem; }
        th { text-align: right; padding: 10px 14px; color: var(--gold); font-weight: 700; border-bottom: 1px solid var(--border); white-space: nowrap; }
        td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,.04); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }
        .text-muted { color: var(--text-muted); }

        /* Status badges */
        .badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: .75rem; font-weight: 700; }
        .badge-pending   { background: rgba(243,156,18,.15);  color: #f39c12; }
        .badge-confirmed { background: rgba(52,152,219,.15);  color: #3498db; }
        .badge-shipped   { background: rgba(155,89,182,.15);  color: #9b59b6; }
        .badge-delivered { background: rgba(39,174,96,.15);   color: #2ecc71; }
        .badge-cancelled { background: rgba(231,76,60,.15);   color: #e74c3c; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-family: inherit; font-size: .85rem; font-weight: 700; cursor: pointer; text-decoration: none; border: 1px solid transparent; transition: opacity .15s, background .15s; }
        .btn-primary { background: var(--gold); color: #111; border-color: var(--gold); }
        .btn-primary:hover { opacity: .85; }
        .btn-outline { background: transparent; color: var(--text-muted); border-color: var(--border); }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold); }
        .btn-danger { background: rgba(231,76,60,.1); color: var(--danger); border-color: rgba(231,76,60,.3); }
        .btn-danger:hover { background: rgba(231,76,60,.2); }
        .btn-sm { padding: 5px 12px; font-size: .8rem; }

        /* Form */
        .form-inline { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        input[type=text], input[type=password], select, textarea { background: var(--bg); border: 1px solid var(--border); border-radius: 8px; color: var(--text); font-family: inherit; font-size: .9rem; padding: 9px 14px; outline: none; transition: border-color .2s; }
        input[type=text]:focus, input[type=password]:focus, select:focus, textarea:focus { border-color: var(--gold); }
        select option { background: var(--bg-card); }
        label { display: block; font-size: .85rem; color: var(--text-muted); margin-bottom: 6px; font-weight: 600; }
        .error-text { color: var(--danger); font-size: .8rem; margin-top: 4px; }

        /* Stats row */
        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; margin-bottom: 24px; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px; text-align: center; }
        .stat-card .val { font-size: 1.8rem; font-weight: 800; color: var(--gold); }
        .stat-card .lbl { font-size: .78rem; color: var(--text-muted); margin-top: 4px; }
        .stat-card.active { border-color: var(--gold); background: rgba(212,175,55,.07); }

        /* Pagination */
        .pagination { display: flex; gap: 6px; margin-top: 20px; justify-content: center; flex-wrap: wrap; }
        .pagination a, .pagination span { padding: 6px 13px; border-radius: 7px; font-size: .82rem; font-weight: 700; border: 1px solid var(--border); color: var(--text-muted); text-decoration: none; }
        .pagination a:hover { border-color: var(--gold); color: var(--gold); }
        .pagination span.active { background: var(--gold); color: #111; border-color: var(--gold); }

        /* Filter tabs */
        .filter-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
        .filter-tab { padding: 6px 14px; border-radius: 20px; font-size: .82rem; font-weight: 700; cursor: pointer; border: 1px solid var(--border); color: var(--text-muted); text-decoration: none; transition: background .15s, color .15s, border-color .15s; }
        .filter-tab:hover, .filter-tab.active { background: rgba(212,175,55,.1); color: var(--gold); border-color: var(--gold); }

        /* Search */
        .search-form { display: flex; gap: 8px; }
        .search-form input { min-width: 220px; }

        /* Order detail */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,.05); font-size: .9rem; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: var(--text-muted); }
        .detail-value { font-weight: 600; }
        .items-table th { font-size: .8rem; }

        /* Inline status select */
        .inline-status-form { margin: 0; }
        .status-select {
            padding: 4px 10px; font-size: .78rem; font-weight: 700;
            border-radius: 20px; cursor: pointer; border: 1px solid;
            background: transparent; font-family: inherit; outline: none;
            appearance: auto;
        }
        .status-pending   { color: #f39c12; border-color: rgba(243,156,18,.4); background: rgba(243,156,18,.08); }
        .status-confirmed { color: #3498db; border-color: rgba(52,152,219,.4); background: rgba(52,152,219,.08); }
        .status-shipped   { color: #9b59b6; border-color: rgba(155,89,182,.4); background: rgba(155,89,182,.08); }
        .status-delivered { color: #2ecc71; border-color: rgba(39,174,96,.4);  background: rgba(39,174,96,.08);  }
        .status-cancelled { color: #e74c3c; border-color: rgba(231,76,60,.4);  background: rgba(231,76,60,.08);  }
        .status-select option { background: var(--bg-card); color: var(--text); font-weight: 600; }

        @media (max-width: 900px) {
            .sidebar { display: none; }
            .detail-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <h2><i class="fas fa-store"></i> لوحة التحكم</h2>
        <p>ورشة يحيى للموس البوسعادي</p>
    </div>
    <nav class="sidebar-nav">
        @php
            $pendingCount = \App\Models\Order::where('status','pending')->count();
            $newTickets   = \App\Models\SupportTicket::where('status','new')->count();
        @endphp
        <a href="{{ route('admin.dashboard') }}" class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif">
            <i class="fas fa-chart-bar"></i> لوحة التحكم
        </a>
        <a href="{{ route('admin.analytics') }}" class="nav-link @if(request()->routeIs('admin.analytics')) active @endif">
            <i class="fas fa-chart-line"></i> الإحصائيات
        </a>
        <a href="{{ route('admin.orders') }}" class="nav-link @if(request()->routeIs('admin.orders*')) active @endif">
            <i class="fas fa-shopping-bag"></i> الطلبات
            @if($pendingCount > 0)
            <span style="margin-right:auto; background:var(--gold); color:#111; font-size:.65rem; font-weight:800; padding:2px 7px; border-radius:20px;">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.tickets') }}" class="nav-link @if(request()->routeIs('admin.tickets*')) active @endif">
            <i class="fas fa-headset"></i> رسائل الدعم
            @if($newTickets > 0)
            <span style="margin-right:auto; background:#e74c3c; color:#fff; font-size:.65rem; font-weight:800; padding:2px 7px; border-radius:20px;">{{ $newTickets }}</span>
            @endif
        </a>
        <a href="{{ route('admin.carts') }}" class="nav-link @if(request()->routeIs('admin.carts*')) active @endif">
            <i class="fas fa-shopping-cart"></i> السلة المتروكة
        </a>
        <a href="{{ route('admin.customers') }}" class="nav-link @if(request()->routeIs('admin.customers*')) active @endif">
            <i class="fas fa-users"></i> العملاء
        </a>
        <a href="{{ route('admin.products.index') }}" class="nav-link @if(request()->routeIs('admin.products*')) active @endif">
            <i class="fas fa-box-open"></i> المنتجات
        </a>
        <a href="{{ route('admin.profile') }}" class="nav-link @if(request()->routeIs('admin.profile')) active @endif">
            <i class="fas fa-user-shield"></i> الملف الشخصي
        </a>
        <a href="{{ route('home') }}" target="_blank" class="nav-link">
            <i class="fas fa-external-link-alt"></i> المتجر
        </a>
    </nav>
    <div class="sidebar-footer">
        <div style="padding: 8px 14px 12px; font-size:.8rem;">
            <div style="color:var(--gold); font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
            <div style="color:var(--text-muted); font-size:.72rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-top:2px;">{{ Auth::user()->email }}</div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</button>
        </form>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <div>
            <div class="topbar-title">@yield('page-title', 'لوحة التحكم')</div>
            <div class="topbar-sub">ورشة يحيى | {{ now()->format('Y/m/d') }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            @yield('topbar-actions')
            {{-- Sound toggle --}}
            <button id="soundToggleBtn" title="تفعيل/إيقاف الصوت" onclick="window._notif && window._notif.toggleMute()" style="
                background:rgba(212,175,55,.1);border:1px solid rgba(212,175,55,.25);
                color:var(--gold);border-radius:8px;padding:7px 12px;cursor:pointer;
                font-size:.82rem;font-weight:700;font-family:inherit;display:flex;align-items:center;gap:6px;
                transition:background .15s;">
                <i id="soundIcon" class="fas fa-bell"></i>
                <span id="soundLabel">الصوت</span>
            </button>
        </div>
    </div>

    <div class="content">
        @if (session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if ($errors->any())
        <div class="alert alert-error"><i class="fas fa-times-circle"></i> {{ $errors->first() }}</div>
        @endif

        @yield('content')
    </div>
</div>


{{-- ── Order notification system ────────────────────────────────────────── --}}
<div id="orderToast" style="
    display:none;position:fixed;bottom:28px;right:28px;z-index:9999;
    background:#1a1a10;border:1px solid var(--gold);border-radius:14px;
    padding:18px 20px;min-width:280px;max-width:320px;
    box-shadow:0 12px 40px rgba(0,0,0,.7);
" role="alert" aria-live="assertive">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="font-size:2rem;flex-shrink:0;">🎉</div>
        <div style="flex:1;min-width:0;">
            <div style="color:var(--gold);font-weight:800;font-size:.95rem;">طلب جديد!</div>
            <div id="orderToastMsg" style="color:var(--text-muted);font-size:.8rem;margin-top:3px;"></div>
        </div>
        <button onclick="window._notif.closeToast()"
                style="flex-shrink:0;background:none;border:none;color:var(--text-muted);
                       cursor:pointer;font-size:1.1rem;padding:4px;line-height:1;
                       transition:color .15s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color=''">✕</button>
    </div>
    <a id="orderToastLink" href="{{ route('admin.orders') }}"
       style="display:block;margin-top:12px;text-align:center;background:var(--gold);
              color:#111;border-radius:8px;padding:8px;font-size:.85rem;
              font-weight:800;text-decoration:none;transition:opacity .15s;"
       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
        عرض الطلبات &larr;
    </a>
</div>

<style>
@keyframes _toastIn  { from { opacity:0; transform:translateY(16px) scale(.97); } to { opacity:1; transform:none; } }
@keyframes _toastOut { from { opacity:1; transform:none; } to { opacity:0; transform:translateY(8px); } }
#soundToggleBtn:hover { background:rgba(212,175,55,.2) !important; }
#soundToggleBtn.muted { opacity:.5; }
</style>

<script>
window._notif = (function() {
    'use strict';

    const STORE_KEY  = 'yhy_last_order_id';
    const MUTE_KEY   = 'yhy_sound_muted';
    const POLL_MS    = 28000;

    // ── Single shared AudioContext — unlocked by first user gesture ──────────
    let _ctx  = null;
    let _muted = localStorage.getItem(MUTE_KEY) === '1';

    function getCtx() {
        if (!_ctx) {
            _ctx = new (window.AudioContext || window.webkitAudioContext)();
        }
        // Resume if suspended (browser autoplay policy)
        if (_ctx.state === 'suspended') _ctx.resume();
        return _ctx;
    }

    // Unlock on first real user interaction
    function unlockAudio() {
        if (_ctx && _ctx.state === 'running') return;
        getCtx();
        document.removeEventListener('click', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
    }
    document.addEventListener('click',   unlockAudio, { once: false });
    document.addEventListener('keydown', unlockAudio, { once: false });

    // ── Chime: Shopify-style ka-ching cash register ──────────────────────────
    function playChime() {
        if (_muted) return;
        try {
            const ctx = getCtx();
            const t   = ctx.currentTime;

            // Master compressor for clean output
            const comp = ctx.createDynamicsCompressor();
            comp.threshold.value = -12; comp.knee.value = 6;
            comp.ratio.value = 4; comp.attack.value = 0.001; comp.release.value = 0.15;
            comp.connect(ctx.destination);

            // "Ka" — mechanical register click (filtered noise burst)
            const bufLen   = Math.floor(ctx.sampleRate * 0.06);
            const clickBuf = ctx.createBuffer(1, bufLen, ctx.sampleRate);
            const d        = clickBuf.getChannelData(0);
            for (let i = 0; i < bufLen; i++) d[i] = (Math.random()*2-1) * Math.pow(1-i/bufLen, 4);
            const click = ctx.createBufferSource(); click.buffer = clickBuf;
            const hpf   = ctx.createBiquadFilter(); hpf.type = 'highpass'; hpf.frequency.value = 2000; hpf.Q.value = 1;
            const cg    = ctx.createGain(); cg.gain.value = 0.5;
            click.connect(hpf); hpf.connect(cg); cg.connect(comp);
            click.start(t); click.stop(t + 0.07);

            // "Ching" — metallic cash register bell ring (starts 25ms after click)
            const cs = t + 0.025;
            [[1318.51,'triangle',0.35,0.55],[1760.00,'triangle',0.25,0.45],[2093.00,'sine',0.15,0.38],[2637.02,'sine',0.09,0.28]]
            .forEach(([freq, type, amp, dur]) => {
                const o = ctx.createOscillator(); o.type = type; o.frequency.value = freq;
                const g = ctx.createGain();
                g.gain.setValueAtTime(0, cs);
                g.gain.linearRampToValueAtTime(amp, cs + 0.008);
                g.gain.exponentialRampToValueAtTime(0.0001, cs + dur);
                o.connect(g); g.connect(comp);
                o.start(cs); o.stop(cs + dur + 0.05);
            });

            // High shimmer 80ms later (the bright "ing" tail)
            const ss = t + 0.08;
            [[2793.83,'sine',0.08,0.22],[3136.00,'sine',0.05,0.18]]
            .forEach(([freq, type, amp, dur]) => {
                const o = ctx.createOscillator(); o.type = type; o.frequency.value = freq;
                const g = ctx.createGain();
                g.gain.setValueAtTime(0, ss);
                g.gain.linearRampToValueAtTime(amp, ss + 0.005);
                g.gain.exponentialRampToValueAtTime(0.0001, ss + dur);
                o.connect(g); g.connect(comp);
                o.start(ss); o.stop(ss + dur + 0.05);
            });
        } catch (e) {}
    }

    // ── Toast show / hide ────────────────────────────────────────────────────
    let _toastTimer = null;

    function showToast(msg) {
        const toast = document.getElementById('orderToast');
        const msgEl = document.getElementById('orderToastMsg');
        if (!toast) return;
        if (msgEl) msgEl.textContent = msg;
        clearTimeout(_toastTimer);
        toast.style.animation = 'none';
        toast.offsetHeight;            // force reflow
        toast.style.display   = 'block';
        toast.style.animation = '_toastIn .35s cubic-bezier(.22,.68,0,1.2) forwards';
        _toastTimer = setTimeout(closeToast, 9000);
    }

    function closeToast() {
        const toast = document.getElementById('orderToast');
        if (!toast || toast.style.display === 'none') return;
        toast.style.animation = '_toastOut .25s ease forwards';
        setTimeout(() => { toast.style.display = 'none'; }, 260);
    }

    // ── Mute toggle ──────────────────────────────────────────────────────────
    function updateMuteUI() {
        const btn   = document.getElementById('soundToggleBtn');
        const icon  = document.getElementById('soundIcon');
        const label = document.getElementById('soundLabel');
        if (!btn) return;
        if (_muted) {
            btn.classList.add('muted');
            if (icon)  icon.className  = 'fas fa-bell-slash';
            if (label) label.textContent = 'مكتوم';
        } else {
            btn.classList.remove('muted');
            if (icon)  icon.className  = 'fas fa-bell';
            if (label) label.textContent = 'الصوت';
        }
    }

    function toggleMute() {
        _muted = !_muted;
        localStorage.setItem(MUTE_KEY, _muted ? '1' : '0');
        updateMuteUI();
        if (!_muted) {
            // Play a test note so admin hears it's working
            playChime();
        }
    }

    updateMuteUI();

    // ── Real-time order stream via Server-Sent Events ────────────────────────
    let _es = null;
    let _reconnTimer = null;

    function handleOrderEvent(data) {
        const newId  = data.id || 0;
        const lastId = parseInt(localStorage.getItem(STORE_KEY) || '0');

        if (data.init) {
            if (!lastId) localStorage.setItem(STORE_KEY, newId);
            return;
        }

        if (newId > lastId) {
            const diff = newId - lastId;
            localStorage.setItem(STORE_KEY, newId);
            playChime();
            showToast(diff > 1 ? `وصل ${diff} طلبات جديدة!` : 'وصل طلب جديد للتو!');
            const badge = document.querySelector('.sidebar-nav a[href*="/orders"] span');
            if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent || 0) + diff);
        } else if (newId > 0) {
            localStorage.setItem(STORE_KEY, newId);
        }
    }

    function connectSSE() {
        if (_es) { _es.close(); _es = null; }
        _es = new EventSource('{{ route("admin.orders.stream") }}');
        _es.onmessage = function(e) {
            try { handleOrderEvent(JSON.parse(e.data)); } catch(ex) {}
        };
        _es.onerror = function() {
            _es.close(); _es = null;
            clearTimeout(_reconnTimer);
            _reconnTimer = setTimeout(connectSSE, 10000);
        };
    }

    connectSSE();

    return { toggleMute, closeToast, playChime };
})();
</script>

</body>
</html>
