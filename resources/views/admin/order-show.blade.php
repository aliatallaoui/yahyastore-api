@extends('admin.layout')

@section('title', 'الطلب ' . $order->order_number)
@section('page-title', 'تفاصيل الطلب')

@section('topbar-actions')
<a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="btn btn-outline btn-sm">
    <i class="fas fa-print"></i> طباعة
</a>
<a href="{{ route('admin.orders') }}" class="btn btn-outline btn-sm"><i class="fas fa-arrow-right"></i> العودة</a>
@endsection

@section('content')

@php
$badgeClass = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'][$order->status] ?? '';
$statusLabel = ['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'][$order->status] ?? $order->status;
@endphp

<div style="display:flex; align-items:center; gap:14px; margin-bottom:22px; flex-wrap:wrap;">
    <h2 style="font-size:1.2rem; color:var(--gold); font-family:monospace;">{{ $order->order_number }}</h2>
    <span class="badge {{ $badgeClass }}" style="font-size:.85rem; padding:5px 16px;">{{ $statusLabel }}</span>
    <span class="text-muted" style="font-size:.82rem;">{{ $order->created_at->format('Y/m/d H:i') }}</span>
</div>

<div class="detail-grid">

    {{-- Customer Info --}}
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-user"></i> بيانات العميل</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">{{ $order->name }}</span></div>
            <div class="detail-row"><span class="detail-label">الهاتف</span>
                <span class="detail-value" style="direction:ltr;">{{ $order->phone }}</span>
            </div>
            <div class="detail-row"><span class="detail-label">الولاية</span><span class="detail-value">{{ $order->wilaya_name }} ({{ $order->wilaya_code }})</span></div>
            <div class="detail-row"><span class="detail-label">العنوان</span><span class="detail-value" style="text-align:left; max-width:60%;">{{ $order->address }}</span></div>
            @if ($order->notes)
            <div class="detail-row"><span class="detail-label">ملاحظات</span><span class="detail-value" style="color:var(--warning);">{{ $order->notes }}</span></div>
            @endif
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-receipt"></i> ملخص المالي</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">المجموع الفرعي</span><span class="detail-value">{{ number_format($order->subtotal, 0, '.', ',') }} DZD</span></div>
            <div class="detail-row"><span class="detail-label">الشحن</span><span class="detail-value">{{ number_format($order->shipping, 0, '.', ',') }} DZD</span></div>
            <div class="detail-row" style="border-bottom:none;">
                <span class="detail-label" style="font-size:1rem; font-weight:800; color:var(--text);">الإجمالي</span>
                <span class="detail-value" style="font-size:1.2rem; color:var(--gold);">{{ number_format($order->total, 0, '.', ',') }} DZD</span>
            </div>
            <div style="margin-top:16px;">
                <span class="badge badge-pending" style="font-size:.82rem;">الدفع عند الاستلام (COD)</span>
            </div>
        </div>
    </div>
</div>

{{-- Items --}}
<div class="card" style="margin-top:18px;">
    <div class="card-header"><h3><i class="fas fa-box-open"></i> المنتجات المطلوبة</h3></div>
    <div style="overflow-x:auto;">
        <table class="items-table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>المجموع</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td style="font-weight:700;">{{ $item->product_name }}</td>
                    <td>{{ number_format($item->price, 0, '.', ',') }} DZD</td>
                    <td>{{ $item->qty }}</td>
                    <td style="font-weight:700; color:var(--gold);">{{ number_format($item->subtotal, 0, '.', ',') }} DZD</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Actions --}}
<div class="card" style="margin-top:18px;">
    <div class="card-header"><h3><i class="fas fa-cogs"></i> تحديث الحالة</h3></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="form-inline">
            @csrf
            <select name="status">
                @foreach (['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'] as $val => $label)
                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
        </form>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
            @if ($order->whatsapp_url)
            <a href="{{ $order->whatsapp_url }}" target="_blank" class="btn" style="background:#25d366;color:#fff;">
                <i class="fab fa-whatsapp"></i> رسالة الطلب
            </a>
            @endif

            <a href="tel:{{ $order->phone }}" class="btn btn-outline">
                <i class="fas fa-phone"></i> اتصال
            </a>

            <form method="POST" action="{{ route('admin.orders.delete', $order) }}"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')" style="margin-right:auto;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> حذف الطلب</button>
            </form>
        </div>

        @php
            $custPhone = '213' . ltrim($order->phone, '0');
            $waMessages = [
                'confirmed' => "السلام عليكم {$order->name}،\nتم تأكيد طلبك رقم *{$order->order_number}* بنجاح ✅\n\nسنتواصل معك قريباً لتحديد موعد التسليم.\n\n🗡️ *ورشة يحيى للموس البوسعادي*",
                'shipped'   => "السلام عليكم {$order->name}،\nتم إرسال طلبك رقم *{$order->order_number}* 📦\n\nيصلك خلال 2-5 أيام عمل. لأي استفسار تواصل معنا.\n\n🗡️ *ورشة يحيى للموس البوسعادي*",
                'delivered' => "السلام عليكم {$order->name}،\nنتمنى أن يكون طلبك رقم *{$order->order_number}* وصلك بسلامة ✅\n\nشكراً لثقتك بنا! نتمنى أن يعجبك المنتج ❤️\n\n🗡️ *ورشة يحيى للموس البوسعادي*",
            ];
        @endphp

        <div style="margin-top:20px; border-top:1px solid var(--border); padding-top:16px;">
            <div style="font-size:.82rem; font-weight:700; color:var(--text-muted); margin-bottom:10px;">
                <i class="fab fa-whatsapp" style="color:#25d366;"></i> إشعار الزبون على واتساب
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="https://wa.me/{{ $custPhone }}?text={{ rawurlencode($waMessages['confirmed']) }}" target="_blank"
                   class="btn btn-sm" style="background:rgba(52,152,219,.1);color:#3498db;border-color:rgba(52,152,219,.3);">
                    ✅ تم التأكيد
                </a>
                <a href="https://wa.me/{{ $custPhone }}?text={{ rawurlencode($waMessages['shipped']) }}" target="_blank"
                   class="btn btn-sm" style="background:rgba(155,89,182,.1);color:#9b59b6;border-color:rgba(155,89,182,.3);">
                    📦 تم الشحن
                </a>
                <a href="https://wa.me/{{ $custPhone }}?text={{ rawurlencode($waMessages['delivered']) }}" target="_blank"
                   class="btn btn-sm" style="background:rgba(39,174,96,.1);color:#2ecc71;border-color:rgba(39,174,96,.3);">
                    🎉 تم التسليم
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
