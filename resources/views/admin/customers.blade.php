@extends('admin.layout')

@section('title', 'العملاء')
@section('page-title', 'قاعدة العملاء')

@section('content')

{{-- Search --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" action="{{ route('admin.customers') }}" class="search-form">
            <input type="text" name="q" value="{{ $search }}" placeholder="بحث باسم أو رقم هاتف..."
                   style="flex:1;max-width:340px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> بحث</button>
            @if($search)
            <a href="{{ route('admin.customers') }}" class="btn btn-outline btn-sm">✕ إلغاء</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>{{ $customers->total() }} عميل</h3>
        <div style="font-size:.78rem;color:var(--text-muted);">مجمّع من جميع الطلبات • مرتّب حسب آخر طلب</div>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>الولاية</th>
                    <th>الطلبات</th>
                    <th>إجمالي الإنفاق</th>
                    <th>آخر طلب</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                @php
                    $waPhone = '213' . ltrim($c->phone, '0');
                    $isVip   = $c->orders_count >= 3 || $c->total_spent >= 15000;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700;display:flex;align-items:center;gap:8px;">
                            {{ $c->name }}
                            @if($isVip)
                            <span style="font-size:.65rem;font-weight:800;background:rgba(212,175,55,.2);color:var(--gold);padding:2px 8px;border-radius:20px;">⭐ VIP</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-size:.88rem;direction:ltr;display:inline-block;">{{ $c->phone }}</span>
                    </td>
                    <td class="text-muted" style="font-size:.85rem;">{{ $c->wilaya }}</td>
                    <td>
                        <span style="font-weight:700;{{ $c->orders_count >= 3 ? 'color:var(--gold)' : '' }}">
                            {{ $c->orders_count }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:700;color:var(--gold);">{{ number_format($c->total_spent, 0, '.', ',') }} DZD</span>
                    </td>
                    <td class="text-muted" style="font-size:.8rem;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($c->last_order_at)->diffForHumans() }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.orders') }}?q={{ urlencode($c->phone) }}"
                               class="btn btn-outline btn-sm" title="عرض الطلبات">
                                <i class="fas fa-list"></i>
                            </a>
                            <a href="https://wa.me/{{ $waPhone }}" target="_blank"
                               class="btn btn-sm" style="background:#25d366;color:#fff;border-color:#25d366;">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="tel:{{ $c->phone }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-phone"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:50px;color:var(--text-muted);">
                        <i class="fas fa-users" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                        لا يوجد عملاء بعد
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div style="padding:16px 20px;">
        {{ $customers->links() }}
    </div>
    @endif
</div>

@endsection
