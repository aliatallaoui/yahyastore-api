@extends('admin.layout')

@section('title', 'الإحصائيات')
@section('page-title', 'الإحصائيات والتحليلات')

@section('content')

@php
$eventLabels = ['pageview'=>'زيارة صفحة','product_view'=>'مشاهدة منتج','add_to_cart'=>'إضافة للسلة','checkout_start'=>'بدء الطلب'];
$eventIcons  = ['pageview'=>'👁','product_view'=>'📦','add_to_cart'=>'🛒','checkout_start'=>'📋'];
$conv = fn($a,$b) => $b > 0 ? round($a/$b*100, 1) : 0;
@endphp

<style>
.an-grid   { display:grid; gap:14px; }
.an-2col   { grid-template-columns:1fr 1fr; }
.an-3col   { grid-template-columns:1fr 1fr 1fr; }
.kpi-row   { grid-template-columns:repeat(6,1fr); }
.an-card   { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; }
.an-head   { padding:14px 18px; border-bottom:1px solid var(--border); font-size:.88rem; font-weight:800; color:var(--text); display:flex; align-items:center; gap:8px; }
.an-head i { color:var(--gold); }
.an-body   { padding:18px; }
.kpi-card  { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); padding:16px 14px; text-align:center; }
.kpi-val   { font-size:1.7rem; font-weight:900; color:var(--gold); line-height:1; }
.kpi-lbl   { font-size:.72rem; color:var(--text-muted); margin-top:5px; line-height:1.3; }
.kpi-sub   { font-size:.8rem; color:var(--text-muted); margin-top:3px; }
.kpi-card.highlight { border-color:var(--gold); background:rgba(212,175,55,.06); }
.funnel-step { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.funnel-bar-wrap { flex:1; background:rgba(255,255,255,.05); border-radius:4px; height:28px; overflow:hidden; }
.funnel-bar  { height:100%; border-radius:4px; display:flex; align-items:center; padding:0 10px; font-size:.78rem; font-weight:700; color:#111; white-space:nowrap; transition:width .6s ease; }
.funnel-label{ width:130px; font-size:.8rem; color:var(--text-muted); text-align:left; }
.funnel-count{ width:60px; font-size:.82rem; font-weight:700; color:var(--text); text-align:left; }
.funnel-pct  { width:48px; font-size:.72rem; color:var(--text-muted); }
.top-row     { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid rgba(255,255,255,.04); font-size:.84rem; }
.top-row:last-child { border-bottom:none; }
.top-bar-wrap{ flex:1; margin:0 10px; background:rgba(255,255,255,.05); border-radius:3px; height:6px; overflow:hidden; }
.top-bar     { height:100%; border-radius:3px; background:var(--gold); opacity:.7; }
.top-name    { max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:var(--text); }
.top-count   { font-weight:700; color:var(--gold); font-size:.82rem; white-space:nowrap; }
.device-ring { display:flex; align-items:center; justify-content:center; gap:24px; padding:10px 0; }
.device-item { text-align:center; }
.device-pct  { font-size:2rem; font-weight:900; color:var(--gold); }
.device-lbl  { font-size:.75rem; color:var(--text-muted); margin-top:4px; }
.event-pill  { display:inline-block; padding:2px 8px; border-radius:12px; font-size:.7rem; font-weight:700; }
.pill-pv     { background:rgba(52,152,219,.15); color:#3498db; }
.pill-prod   { background:rgba(212,175,55,.15); color:var(--gold); }
.pill-cart   { background:rgba(39,174,96,.15); color:#2ecc71; }
.pill-co     { background:rgba(155,89,182,.15); color:#9b59b6; }
@media(max-width:1100px){ .kpi-row{ grid-template-columns:repeat(3,1fr); } .an-2col,.an-3col{ grid-template-columns:1fr; } }
@media(max-width:700px) { .kpi-row{ grid-template-columns:repeat(2,1fr); } }
</style>

{{-- KPI row --}}
<div class="an-grid kpi-row" style="margin-bottom:20px;">

    <div class="kpi-card highlight">
        <div style="font-size:1.4rem;margin-bottom:4px;">👁</div>
        <div class="kpi-val">{{ number_format($kpi['visits_today']) }}</div>
        <div class="kpi-lbl">زيارات اليوم</div>
        <div class="kpi-sub">{{ $kpi['unique_today'] }} فريد</div>
    </div>

    <div class="kpi-card">
        <div style="font-size:1.4rem;margin-bottom:4px;">📅</div>
        <div class="kpi-val">{{ number_format($kpi['visits_30']) }}</div>
        <div class="kpi-lbl">زيارات 30 يوم</div>
        <div class="kpi-sub">{{ number_format($kpi['unique_30']) }} فريد</div>
    </div>

    <div class="kpi-card">
        <div style="font-size:1.4rem;margin-bottom:4px;">📦</div>
        <div class="kpi-val">{{ number_format($kpi['product_views_30']) }}</div>
        <div class="kpi-lbl">مشاهدات المنتجات</div>
        <div class="kpi-sub">آخر 30 يوم</div>
    </div>

    <div class="kpi-card">
        <div style="font-size:1.4rem;margin-bottom:4px;">🛒</div>
        <div class="kpi-val">{{ number_format($kpi['add_to_cart_30']) }}</div>
        <div class="kpi-lbl">إضافة للسلة</div>
        <div class="kpi-sub">{{ $kpi['checkout_30'] }} بدأ الطلب</div>
    </div>

    <div class="kpi-card {{ $kpi['orders_today'] > 0 ? 'highlight' : '' }}">
        <div style="font-size:1.4rem;margin-bottom:4px;">🎯</div>
        <div class="kpi-val" style="{{ $kpi['orders_today'] > 0 ? 'color:#2ecc71' : '' }}">{{ $kpi['orders_today'] }}</div>
        <div class="kpi-lbl">طلبات اليوم</div>
        <div class="kpi-sub">{{ number_format($kpi['orders_30']) }} / 30 يوم</div>
    </div>

    <div class="kpi-card">
        <div style="font-size:1.4rem;margin-bottom:4px;">💰</div>
        <div class="kpi-val" style="font-size:1.1rem;">{{ number_format($kpi['revenue_30'], 0, '.', ',') }}</div>
        <div class="kpi-lbl">إجمالي المبيعات (DZD)</div>
        <div class="kpi-sub">{{ number_format($kpi['revenue_today'], 0, '.', ',') }} اليوم</div>
    </div>

</div>

{{-- Chart + Funnel --}}
<div class="an-grid an-2col" style="margin-bottom:20px;">

    {{-- Traffic chart --}}
    <div class="an-card">
        <div class="an-head"><i class="fas fa-chart-line"></i> الزيارات اليومية — آخر 14 يوم</div>
        <div class="an-body" style="padding-bottom:10px;">
            <canvas id="trafficChart" height="130"></canvas>
        </div>
    </div>

    {{-- Conversion funnel --}}
    <div class="an-card">
        <div class="an-head"><i class="fas fa-filter"></i> قمع التحويل — آخر 30 يوم</div>
        <div class="an-body">
            @php
            $funnelSteps = [
                ['label'=>'زوار فريدون', 'count'=>$funnel['visits'],   'color'=>'#3498db'],
                ['label'=>'شاهدوا منتجاً', 'count'=>$funnel['views'],  'color'=>'#9b59b6'],
                ['label'=>'أضافوا للسلة', 'count'=>$funnel['cart'],    'color'=>'#f39c12'],
                ['label'=>'بدأوا الطلب',  'count'=>$funnel['checkout'],'color'=>'#e67e22'],
                ['label'=>'أكملوا الطلب', 'count'=>$funnel['orders'],  'color'=>'#2ecc71'],
            ];
            $maxStep = max(1, $funnel['visits']);
            @endphp
            @foreach($funnelSteps as $i => $step)
            <div class="funnel-step">
                <div class="funnel-label">{{ $step['label'] }}</div>
                <div class="funnel-bar-wrap">
                    <div class="funnel-bar" style="width:{{ min(100, round($step['count']/$maxStep*100)) }}%;background:{{ $step['color'] }};">
                        @if($step['count'] > 0) {{ $step['count'] }} @endif
                    </div>
                </div>
                <div class="funnel-count">{{ number_format($step['count']) }}</div>
                <div class="funnel-pct">
                    @if($i > 0)
                        {{ $conv($step['count'], $funnelSteps[$i-1]['count']) }}%
                    @else
                        100%
                    @endif
                </div>
            </div>
            @endforeach

            @if($funnel['visits'] > 0)
            <div style="margin-top:14px;padding:10px 14px;background:rgba(39,174,96,.08);border:1px solid rgba(39,174,96,.2);border-radius:8px;font-size:.82rem;text-align:center;">
                معدل التحويل الإجمالي:
                <span style="color:#2ecc71;font-weight:800;font-size:1rem;margin-right:6px;">
                    {{ $conv($funnel['orders'], $funnel['visits']) }}%
                </span>
                <span style="color:var(--text-muted);">(زوار → طلبات)</span>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Top Pages + Top Products Viewed --}}
<div class="an-grid an-2col" style="margin-bottom:20px;">

    <div class="an-card">
        <div class="an-head"><i class="fas fa-file-alt"></i> أكثر الصفحات زيارةً — 30 يوم</div>
        <div class="an-body">
            @php $maxPv = max(1, $topPages->max('views') ?? 1); @endphp
            @forelse($topPages as $row)
            <div class="top-row">
                <div class="top-name" title="{{ $row->page }}">{{ $row->page ?: '(الرئيسية)' }}</div>
                <div class="top-bar-wrap"><div class="top-bar" style="width:{{ round($row->views/$maxPv*100) }}%"></div></div>
                <div class="top-count">{{ number_format($row->views) }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;">لا توجد بيانات بعد</div>
            @endforelse
        </div>
    </div>

    <div class="an-card">
        <div class="an-head"><i class="fas fa-eye"></i> أكثر المنتجات مشاهدةً — 30 يوم</div>
        <div class="an-body">
            @php $maxVw = max(1, $topViewed->max('views') ?? 1); @endphp
            @forelse($topViewed as $row)
            <div class="top-row">
                <div class="top-name" title="{{ $row->product_name }}">{{ $row->product_name ?: '—' }}</div>
                <div class="top-bar-wrap"><div class="top-bar" style="width:{{ round($row->views/$maxVw*100) }}%"></div></div>
                <div class="top-count">{{ number_format($row->views) }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;">لا توجد بيانات بعد</div>
            @endforelse
        </div>
    </div>

</div>

{{-- Top Cart + Orders by Wilaya + Device --}}
<div class="an-grid an-3col" style="margin-bottom:20px;">

    <div class="an-card">
        <div class="an-head"><i class="fas fa-shopping-cart"></i> أكثر المنتجات إضافةً للسلة</div>
        <div class="an-body">
            @php $maxCt = max(1, $topCart->max('adds') ?? 1); @endphp
            @forelse($topCart as $row)
            <div class="top-row">
                <div class="top-name" title="{{ $row->product_name }}">{{ $row->product_name ?: '—' }}</div>
                <div class="top-bar-wrap"><div class="top-bar" style="width:{{ round($row->adds/$maxCt*100) }}%;background:#2ecc71;"></div></div>
                <div class="top-count">{{ number_format($row->adds) }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;">لا توجد بيانات بعد</div>
            @endforelse
        </div>
    </div>

    <div class="an-card">
        <div class="an-head"><i class="fas fa-map-marker-alt"></i> الطلبات حسب الولاية — 30 يوم</div>
        <div class="an-body">
            @php $maxWl = max(1, $byWilaya->max('cnt') ?? 1); @endphp
            @forelse($byWilaya as $row)
            <div class="top-row">
                <div class="top-name">{{ $row->wilaya_name }}</div>
                <div class="top-bar-wrap"><div class="top-bar" style="width:{{ round($row->cnt/$maxWl*100) }}%;background:#9b59b6;"></div></div>
                <div class="top-count">{{ $row->cnt }}</div>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;">لا توجد طلبات بعد</div>
            @endforelse
        </div>
    </div>

    <div class="an-card">
        <div class="an-head"><i class="fas fa-mobile-alt"></i> الأجهزة — 30 يوم</div>
        <div class="an-body">
            @php
            $total  = max(1, $mobileCount + $desktopCount);
            $mobPct = round($mobileCount / $total * 100);
            $dskPct = 100 - $mobPct;
            @endphp
            <div class="device-ring">
                <div class="device-item">
                    <div class="device-pct" style="color:#3498db;">{{ $mobPct }}%</div>
                    <div style="font-size:1.8rem;margin:6px 0;">📱</div>
                    <div class="device-lbl">موبايل</div>
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px;">{{ number_format($mobileCount) }} زائر</div>
                </div>
                <div style="width:1px;height:80px;background:var(--border);"></div>
                <div class="device-item">
                    <div class="device-pct" style="color:var(--gold);">{{ $dskPct }}%</div>
                    <div style="font-size:1.8rem;margin:6px 0;">🖥️</div>
                    <div class="device-lbl">كمبيوتر</div>
                    <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px;">{{ number_format($desktopCount) }} زائر</div>
                </div>
            </div>

            {{-- Mini stacked bar --}}
            <div style="height:10px;border-radius:5px;overflow:hidden;display:flex;margin-top:10px;">
                <div style="width:{{ $mobPct }}%;background:#3498db;"></div>
                <div style="flex:1;background:var(--gold);opacity:.7;"></div>
            </div>

            {{-- Conversion rate highlight --}}
            @if($kpi['unique_30'] > 0)
            <div style="margin-top:20px;border-top:1px solid var(--border);padding-top:16px;">
                <div style="font-size:.75rem;color:var(--text-muted);margin-bottom:8px;font-weight:700;">معدلات التحويل (30 يوم)</div>
                @php
                $u30 = max(1, $kpi['unique_30']);
                $rates = [
                    ['label'=>'زيارة → منتج',  'val'=> $conv($funnel['views'],    $u30)],
                    ['label'=>'زيارة → سلة',   'val'=> $conv($funnel['cart'],     $u30)],
                    ['label'=>'زيارة → طلب',   'val'=> $conv($funnel['orders'],   $u30)],
                ];
                @endphp
                @foreach($rates as $r)
                <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:.8rem;">
                    <span style="color:var(--text-muted);">{{ $r['label'] }}</span>
                    <span style="font-weight:800;color:{{ $r['val'] >= 5 ? '#2ecc71' : ($r['val'] >= 1 ? 'var(--gold)' : '#e74c3c') }};">
                        {{ $r['val'] }}%
                    </span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Recent events feed --}}
<div class="an-card">
    <div class="an-head"><i class="fas fa-stream"></i> آخر الأحداث</div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>الحدث</th>
                    <th>الصفحة / المنتج</th>
                    <th>الجلسة</th>
                    <th>IP</th>
                    <th>الوقت</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEvents as $ev)
                @php
                $pillClass = ['pageview'=>'pill-pv','product_view'=>'pill-prod','add_to_cart'=>'pill-cart','checkout_start'=>'pill-co'][$ev->event] ?? 'pill-pv';
                @endphp
                <tr>
                    <td>
                        <span class="event-pill {{ $pillClass }}">
                            {{ $eventIcons[$ev->event] ?? '' }} {{ $eventLabels[$ev->event] ?? $ev->event }}
                        </span>
                    </td>
                    <td style="font-size:.8rem;color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $ev->product_name ?: ($ev->page ?: '—') }}
                    </td>
                    <td style="font-family:monospace;font-size:.75rem;color:var(--text-muted);">{{ substr($ev->session_id ?? '—', 0, 10) }}…</td>
                    <td style="font-size:.78rem;color:var(--text-muted);direction:ltr;">{{ $ev->ip ?? '—' }}</td>
                    <td style="font-size:.78rem;color:var(--text-muted);white-space:nowrap;">{{ $ev->created_at?->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="fas fa-chart-line" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                        لا توجد بيانات بعد — ابدأ بزيارة المتجر لتتدفق البيانات
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function() {
    const labels = @json($chartLabels);
    const visits = @json($chartVisits);
    const orders = @json($chartOrders);

    const ctx = document.getElementById('trafficChart').getContext('2d');
    new Chart(ctx, {
        data: {
            labels,
            datasets: [
                {
                    type: 'line',
                    label: 'زوار فريدون',
                    data: visits,
                    borderColor: '#d4af37',
                    backgroundColor: 'rgba(212,175,55,.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#d4af37',
                    pointRadius: 3,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y',
                },
                {
                    type: 'bar',
                    label: 'طلبات',
                    data: orders,
                    backgroundColor: 'rgba(39,174,96,.5)',
                    borderColor: '#2ecc71',
                    borderWidth: 1,
                    borderRadius: 4,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { labels: { color: '#888', font: { family: 'Cairo', size: 11 }, boxWidth: 12 } },
                tooltip: { backgroundColor: '#1a1a10', borderColor: 'rgba(212,175,55,.3)', borderWidth: 1, titleColor: '#d4af37', bodyColor: '#e8e8d8', titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' } },
            },
            scales: {
                x: { ticks: { color: '#666', font: { family: 'Cairo', size: 10 } }, grid: { color: 'rgba(255,255,255,.04)' } },
                y:  { position: 'right', ticks: { color: '#666', font: { family: 'Cairo', size: 10 } }, grid: { color: 'rgba(255,255,255,.04)' }, title: { display: true, text: 'زوار', color: '#d4af37', font: { family: 'Cairo', size: 10 } } },
                y1: { position: 'left',  ticks: { color: '#666', font: { family: 'Cairo', size: 10 } }, grid: { display: false }, title: { display: true, text: 'طلبات', color: '#2ecc71', font: { family: 'Cairo', size: 10 } } },
            },
        },
    });
})();
</script>

@endsection
