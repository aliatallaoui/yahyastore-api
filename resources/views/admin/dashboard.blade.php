@extends('admin.layout')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')

@php
$statusLabel = ['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'];
$badgeClass  = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
@endphp

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:28px;">

    <div class="stat-card">
        <div style="font-size:1.6rem;margin-bottom:6px;opacity:.6;">📦</div>
        <div class="val">{{ $stats['orders_total'] }}</div>
        <div class="lbl">إجمالي الطلبات</div>
    </div>

    <div class="stat-card {{ $stats['orders_pending'] > 0 ? 'active' : '' }}">
        <div style="font-size:1.6rem;margin-bottom:6px;opacity:.6;">⏳</div>
        <div class="val" style="{{ $stats['orders_pending'] > 0 ? 'color:var(--warning)' : '' }}">
            {{ $stats['orders_pending'] }}
        </div>
        <div class="lbl">قيد الانتظار</div>
    </div>

    <div class="stat-card">
        <div style="font-size:1.6rem;margin-bottom:6px;opacity:.6;">🌅</div>
        <div class="val">{{ $stats['orders_today'] }}</div>
        <div class="lbl">طلبات اليوم</div>
    </div>

    <div class="stat-card">
        <div style="font-size:1.6rem;margin-bottom:6px;opacity:.6;">💰</div>
        <div class="val" style="font-size:1.2rem;">{{ number_format($stats['revenue_total'], 0, '.', ',') }}</div>
        <div class="lbl">إجمالي المبيعات (DZD)</div>
    </div>

    <a href="{{ route('admin.tickets', ['status'=>'new']) }}" style="text-decoration:none;">
    <div class="stat-card {{ $stats['tickets_new'] > 0 ? 'active' : '' }}" style="{{ $stats['tickets_new'] > 0 ? 'border-color:var(--danger);background:rgba(231,76,60,.07);' : '' }}">
        <div style="font-size:1.6rem;margin-bottom:6px;opacity:.6;">💬</div>
        <div class="val" style="{{ $stats['tickets_new'] > 0 ? 'color:var(--danger)' : '' }}">
            {{ $stats['tickets_new'] }}
        </div>
        <div class="lbl">رسائل جديدة</div>
    </div>
    </a>

    <a href="{{ route('admin.carts') }}" style="text-decoration:none;">
    <div class="stat-card {{ $stats['carts_abandoned'] > 0 ? 'active' : '' }}" style="{{ $stats['carts_abandoned'] > 0 ? 'border-color:rgba(155,89,182,.5);background:rgba(155,89,182,.07);' : '' }}">
        <div style="font-size:1.6rem;margin-bottom:6px;opacity:.6;">🛒</div>
        <div class="val" style="{{ $stats['carts_abandoned'] > 0 ? 'color:#9b59b6' : '' }}">
            {{ $stats['carts_abandoned'] }}
        </div>
        <div class="lbl">سلة متروكة</div>
    </div>
    </a>

</div>

{{-- Sale Countdown + Top Products row --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

    {{-- Sale countdown --}}
    @if($saleActive)
    <div class="card" style="border-color:rgba(231,76,60,.4);background:rgba(231,76,60,.04);">
        <div class="card-header" style="border-bottom-color:rgba(231,76,60,.2);">
            <h3 style="color:#e74c3c;"><i class="fas fa-fire"></i> انطلاقة الموقع — خصم 20%</h3>
            <span style="font-size:.78rem;color:var(--text-muted);">العرض ينتهي 17 مايو 2026</span>
        </div>
        <div class="card-body" style="text-align:center;padding:24px;">
            <div style="font-size:3rem;font-weight:800;color:#e74c3c;line-height:1;">{{ $saleDaysLeft }}</div>
            <div style="font-size:.9rem;color:var(--text-muted);margin-top:6px;">يوم متبقٍ على انتهاء العرض</div>
            <div style="margin-top:16px;font-size:.78rem;color:var(--text-muted);background:rgba(0,0,0,.2);border-radius:8px;padding:10px;">
                <i class="fas fa-info-circle"></i>
                لإعادة الأسعار الأصلية، شغّل:
                <code style="font-family:monospace;color:var(--gold);display:block;margin-top:4px;">php /root/revert_sale.php</code>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-tag"></i> حالة العروض</h3></div>
        <div class="card-body" style="text-align:center;padding:24px;color:var(--text-muted);">
            <i class="fas fa-check-circle" style="font-size:2rem;color:var(--success);display:block;margin-bottom:8px;"></i>
            لا توجد عروض نشطة حالياً
        </div>
    </div>
    @endif

    {{-- Top selling products --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-bar"></i> أكثر المنتجات مبيعاً</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($topProducts as $i => $p)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 16px;{{ !$loop->last ? 'border-bottom:1px solid rgba(255,255,255,.04);' : '' }}">
                <span style="width:22px;height:22px;border-radius:50%;background:rgba(212,175,55,.15);color:var(--gold);font-size:.72rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $i+1 }}</span>
                <span style="flex:1;font-size:.85rem;font-weight:600;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->product_name }}</span>
                <span style="font-size:.8rem;color:var(--gold);font-weight:700;white-space:nowrap;">{{ $p->total_qty }} وحدة</span>
                <span style="font-size:.72rem;color:var(--text-muted);white-space:nowrap;">{{ number_format($p->total_revenue, 0, '.', ',') }} DZD</span>
            </div>
            @empty
            <div style="text-align:center;padding:30px;color:var(--text-muted);font-size:.85rem;">
                لا توجد مبيعات بعد
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Recent Orders --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clock" style="color:var(--gold);margin-left:8px;"></i> آخر الطلبات</h3>
        <a href="{{ route('admin.orders') }}" class="btn btn-outline btn-sm">عرض الكل</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>الزبون</th>
                    <th>الولاية</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $order)
                <tr>
                    <td style="font-family:monospace;color:var(--gold);font-weight:700;">{{ $order->order_number }}</td>
                    <td>
                        <div style="font-weight:700;">{{ $order->name }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);direction:ltr;">{{ $order->phone }}</div>
                    </td>
                    <td class="text-muted">{{ $order->wilaya_name }}</td>
                    <td style="font-weight:700;color:var(--gold);white-space:nowrap;">
                        {{ number_format($order->total, 0, '.', ',') }} DZD
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass[$order->status] ?? '' }}">
                            {{ $statusLabel[$order->status] ?? $order->status }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.8rem;white-space:nowrap;">
                        {{ $order->created_at->diffForHumans() }}
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                        لا توجد طلبات بعد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($stats['orders_pending'] > 0)
<div style="margin-top:16px;padding:14px 20px;background:rgba(243,156,18,.08);border:1px solid rgba(243,156,18,.3);border-radius:var(--radius);display:flex;align-items:center;gap:12px;">
    <i class="fas fa-bell" style="color:var(--warning);font-size:1.1rem;"></i>
    <span style="font-weight:700;color:var(--warning);">{{ $stats['orders_pending'] }} طلب بانتظار المراجعة</span>
    <a href="{{ route('admin.orders', ['status'=>'pending']) }}" class="btn btn-sm" style="margin-right:auto;background:var(--warning);color:#111;border:none;">
        مراجعة الآن
    </a>
</div>
@endif

@if($lowStock->count() > 0)
<div style="margin-top:12px;padding:14px 20px;background:rgba(231,76,60,.06);border:1px solid rgba(231,76,60,.25);border-radius:var(--radius);">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <i class="fas fa-exclamation-triangle" style="color:var(--danger);"></i>
        <span style="font-weight:700;color:var(--danger);">تحذير: مخزون منخفض</span>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-danger" style="margin-right:auto;">إدارة المخزون</a>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        @foreach($lowStock as $p)
        <a href="{{ route('admin.products.edit', $p) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:.78rem;font-weight:700;text-decoration:none;
                  {{ $p->stock == 0 ? 'background:rgba(231,76,60,.15);color:#e74c3c;border:1px solid rgba(231,76,60,.3);' : 'background:rgba(243,156,18,.12);color:#f39c12;border:1px solid rgba(243,156,18,.3);' }}">
            {{ $p->name }}
            <span style="font-family:monospace;">
                {{ $p->stock == 0 ? 'نفد' : $p->stock }}
            </span>
        </a>
        @endforeach
    </div>
</div>
@endif

@endsection
