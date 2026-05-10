@extends('admin.layout')

@section('title', 'السلة المتروكة')
@section('page-title', 'السلة المتروكة')

@section('content')

<style>
.cart-items-list { font-size:.78rem; color:var(--text-muted); line-height:1.7; }
.cart-items-list span { display:block; }
.recovered-badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:.72rem; font-weight:700; background:rgba(39,174,96,.12); color:#2ecc71; }
.abandoned-badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:.72rem; font-weight:700; background:rgba(231,76,60,.12); color:#e74c3c; }
.fresh-badge     { display:inline-block; padding:2px 10px; border-radius:20px; font-size:.72rem; font-weight:700; background:rgba(243,156,18,.12); color:#f39c12; }
</style>

<div class="card">
    <div class="card-header">
        <h3>السلات المتروكة (آخر 7 أيام)</h3>
        <div style="font-size:.82rem; color:var(--text-muted);">
            يتم تسجيل السلة عند فتح نموذج الطلب. السلات التي أُكمل صاحبها الطلب تظهر كـ "تم الطلب".
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>الوقت</th>
                    <th>الهاتف</th>
                    <th>المنتجات</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($carts as $cart)
                @php
                    $isRecovered = $cart->phone && isset($recoveredPhones[$cart->phone]);
                    $minutesAgo  = $cart->created_at->diffInMinutes(now());
                    $isFresh     = $minutesAgo < 30;
                    $custPhone   = $cart->phone ? '213' . ltrim($cart->phone, '0') : null;
                    $waMsg       = $cart->phone ? rawurlencode(
                        "السلام عليكم،\nلاحظنا أنك بدأت طلبًا في ورشة يحيى للموس البوسعادي ولم تكمله.\n" .
                        "هل تحتاج مساعدة أو لديك استفسار؟ نحن هنا لمساعدتك 🗡️"
                    ) : '';
                @endphp
                <tr>
                    <td style="white-space:nowrap;">
                        <div style="font-size:.85rem; font-weight:600;">{{ $cart->created_at->format('d/m H:i') }}</div>
                        <div style="font-size:.72rem; color:var(--text-muted);">{{ $cart->created_at->diffForHumans() }}</div>
                    </td>
                    <td>
                        @if($cart->phone)
                        <span style="font-family:monospace; font-size:.88rem; direction:ltr; display:inline-block;">{{ $cart->phone }}</span>
                        @else
                        <span class="text-muted" style="font-size:.78rem;">لم يُدخل بعد</span>
                        @endif
                    </td>
                    <td>
                        <div class="cart-items-list">
                            @foreach(array_slice($cart->items, 0, 3) as $item)
                            <span>• {{ $item['product_name'] ?? $item['name'] ?? '؟' }}
                                @if(($item['quantity'] ?? $item['qty'] ?? 1) > 1)
                                    × {{ $item['quantity'] ?? $item['qty'] }}
                                @endif
                            </span>
                            @endforeach
                            @if(count($cart->items) > 3)
                            <span style="color:var(--gold);">+ {{ count($cart->items) - 3 }} أخرى</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span style="font-weight:700; color:var(--gold);">{{ number_format($cart->total, 0, '.', ',') }} DZD</span>
                    </td>
                    <td>
                        @if($isRecovered)
                            <span class="recovered-badge"><i class="fas fa-check"></i> تم الطلب</span>
                        @elseif($isFresh)
                            <span class="fresh-badge"><i class="fas fa-clock"></i> جديد</span>
                        @else
                            <span class="abandoned-badge"><i class="fas fa-times"></i> متروكة</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; align-items:center;">
                            @if($custPhone && !$isRecovered)
                            <a href="https://wa.me/{{ $custPhone }}?text={{ $waMsg }}" target="_blank"
                               class="btn btn-sm" style="background:#25d366;color:#fff;border-color:#25d366;">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                            <form method="POST" action="{{ route('admin.carts.delete', $cart) }}"
                                  onsubmit="return confirm('حذف هذا السجل؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:50px; color:var(--text-muted);">
                        <i class="fas fa-shopping-cart" style="font-size:2rem; display:block; margin-bottom:10px; opacity:.3;"></i>
                        لا توجد سلات متروكة خلال آخر 7 أيام
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($carts->hasPages())
    <div style="padding:16px 20px;">
        {{ $carts->links() }}
    </div>
    @endif
</div>

@endsection
