@extends('admin.layout')

@section('title', 'الطلبات')
@section('page-title', 'الطلبات')

@section('topbar-actions')
<form method="GET" action="{{ route('admin.orders') }}" class="search-form">
    @if($status && $status !== 'all')
        <input type="hidden" name="status" value="{{ $status }}">
    @endif
    <input type="text" name="q" placeholder="بحث بالاسم، الهاتف، رقم الطلب..."
           value="{{ $search ?? '' }}" style="min-width:240px;">
    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
    @if($search)
    <a href="{{ route('admin.orders', $status ? ['status' => $status] : []) }}" class="btn btn-outline btn-sm">مسح</a>
    @endif
</form>
@endsection

@section('content')

{{-- Stats --}}
@php
$statusLabels = ['all'=>'الكل','pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'];
@endphp
<div class="stats-row">
    @foreach ($counts as $key => $count)
    <a href="{{ route('admin.orders', ['status' => $key === 'all' ? null : $key]) }}" style="text-decoration:none;">
        <div class="stat-card {{ ($status ?? 'all') === $key ? 'active' : '' }}">
            <div class="val">{{ $count }}</div>
            <div class="lbl">{{ $statusLabels[$key] }}</div>
        </div>
    </a>
    @endforeach
</div>

{{-- Filter tabs --}}
<div style="margin-bottom:18px;" class="filter-tabs">
    @foreach ($statusLabels as $key => $label)
    <a href="{{ route('admin.orders', ['status' => $key === 'all' ? null : $key, 'q' => $search]) }}"
       class="filter-tab {{ ($status ?? 'all') === $key ? 'active' : '' }}">
        {{ $label }}
        <span style="opacity:.7;">({{ $counts[$key] }})</span>
    </a>
    @endforeach
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-shopping-bag"></i> قائمة الطلبات</h3>
        <span class="text-muted" style="font-size:.82rem;">{{ $orders->total() }} طلب</span>
    </div>

    @if ($orders->isEmpty())
    <div class="card-body" style="text-align:center; padding:48px; color:var(--text-muted);">
        <i class="fas fa-inbox" style="font-size:2.5rem; margin-bottom:14px; display:block;"></i>
        لا توجد طلبات
    </div>
    @else
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>الولاية</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr>
                    <td><span style="font-family:monospace; color:var(--gold); font-size:.82rem;">{{ $order->order_number }}</span></td>
                    <td>{{ $order->name }}</td>
                    <td><span style="direction:ltr; display:inline-block;">{{ $order->phone }}</span></td>
                    <td>{{ $order->wilaya_name }}</td>
                    <td style="font-weight:700;">{{ number_format($order->total, 0, '.', ',') }} DZD</td>
                    <td>
                        @php
                        $statusColors = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
                        $statusLabels = ['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'];
                        @endphp
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="inline-status-form">
                            @csrf
                            <select name="status" class="status-select status-{{ $order->status }}"
                                    onchange="this.closest('form').submit()" title="تغيير الحالة">
                                @foreach ($statusLabels as $val => $label)
                                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="text-muted" style="font-size:.8rem;">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm" title="تفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="tel:{{ $order->phone }}" class="btn btn-outline btn-sm" title="اتصال">
                                <i class="fas fa-phone"></i>
                            </a>
                            @if ($order->whatsapp_url)
                            <a href="{{ $order->whatsapp_url }}" target="_blank" class="btn btn-sm" style="background:#25d366;color:#fff;border-color:#25d366;" title="واتساب">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($orders->hasPages())
    <div class="pagination">
        @if ($orders->onFirstPage())
            <span>‹</span>
        @else
            <a href="{{ $orders->previousPageUrl() }}">‹</a>
        @endif

        @foreach ($orders->getUrlRange(max(1, $orders->currentPage()-2), min($orders->lastPage(), $orders->currentPage()+2)) as $page => $url)
            @if ($page == $orders->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}">{{ $page }}</a>
            @endif
        @endforeach

        @if ($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}">›</a>
        @else
            <span>›</span>
        @endif
    </div>
    @endif
    @endif
</div>

@endsection
