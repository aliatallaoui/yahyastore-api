@extends('admin.layout')

@section('title', 'لوحة التحكم')
@section('page-title', 'لوحة التحكم')

@section('content')

@php
$statusLabel = ['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'];
$badgeClass  = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
@endphp

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px;">

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

@endsection
