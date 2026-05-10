<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وصل تسليم — {{ $order->order_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Arial', 'Tahoma', sans-serif; background: #fff; color: #111; font-size: 13px; padding: 24px 28px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 2px solid #d4af37; }
        .store-name { font-size: 19px; font-weight: 800; }
        .store-sub  { font-size: 11px; color: #666; margin-top: 5px; }
        .order-meta { text-align: left; }
        .order-num  { font-size: 17px; font-weight: 800; font-family: monospace; color: #d4af37; }
        .order-date { font-size: 11px; color: #888; margin-top: 4px; }

        .section { margin-bottom: 16px; }
        .section-title { font-size: 11px; font-weight: 800; color: #888; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 20px; }
        .info-row  { display: flex; gap: 6px; font-size: 13px; align-items: baseline; }
        .info-lbl  { color: #666; min-width: 65px; flex-shrink: 0; font-size: 12px; }
        .info-val  { font-weight: 700; }

        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 700; text-align: right; padding: 6px 10px; background: #f5f5f5; border: 1px solid #ddd; }
        td { font-size: 13px; padding: 7px 10px; border: 1px solid #eee; vertical-align: middle; }
        td.num { direction: ltr; text-align: left; }

        .totals { margin-top: 10px; display: flex; justify-content: flex-start; flex-direction: column; align-items: flex-end; gap: 4px; }
        .total-row { display: flex; gap: 28px; font-size: 13px; }
        .total-row.grand { font-size: 15px; font-weight: 800; color: #d4af37; border-top: 2px solid #d4af37; padding-top: 6px; margin-top: 4px; }
        .cod { display: inline-block; background: #fff3cd; color: #856404; border: 1px solid #ffc107; border-radius: 4px; padding: 4px 14px; font-weight: 700; font-size: 12px; margin-top: 10px; }

        .sig-box { margin-top: 28px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .sig-area { border-top: 1px dashed #bbb; padding-top: 8px; font-size: 11px; color: #888; text-align: center; height: 48px; }

        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee; text-align: center; font-size: 11px; color: #aaa; }

        @media print {
            body { padding: 10px 14px; }
            @page { size: A5; margin: 8mm; }
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <div class="store-name">🗡️ ورشة يحيى للموس البوسعادي</div>
        <div class="store-sub">وصل تسليم &mdash; Bon de Livraison</div>
    </div>
    <div class="order-meta">
        <div class="order-num">{{ $order->order_number }}</div>
        <div class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="section">
    <div class="section-title">معلومات الزبون</div>
    <div class="info-grid">
        <div class="info-row"><span class="info-lbl">الاسم:</span><span class="info-val">{{ $order->name }}</span></div>
        <div class="info-row"><span class="info-lbl">الهاتف:</span><span class="info-val" style="direction:ltr;">{{ $order->phone }}</span></div>
        <div class="info-row"><span class="info-lbl">الولاية:</span><span class="info-val">{{ $order->wilaya_name }} ({{ $order->wilaya_code }})</span></div>
        <div class="info-row"><span class="info-lbl">العنوان:</span><span class="info-val">{{ $order->address }}</span></div>
        @if($order->notes)
        <div class="info-row" style="grid-column:1/-1;"><span class="info-lbl">ملاحظات:</span><span class="info-val" style="color:#b45309;">{{ $order->notes }}</span></div>
        @endif
    </div>
</div>

<div class="section">
    <div class="section-title">المنتجات المطلوبة</div>
    <table>
        <thead>
            <tr>
                <th style="width:28px; text-align:center;">#</th>
                <th>المنتج</th>
                <th style="width:50px; text-align:center;">الكمية</th>
                <th style="width:110px;">سعر الوحدة</th>
                <th style="width:110px;">المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $i => $item)
            <tr>
                <td style="text-align:center; color:#888;">{{ $i + 1 }}</td>
                <td style="font-weight:600;">{{ $item->product_name }}</td>
                <td style="text-align:center;">{{ $item->qty }}</td>
                <td class="num">{{ number_format($item->price, 0, '.', ',') }} DZD</td>
                <td class="num" style="font-weight:700;">{{ number_format($item->subtotal, 0, '.', ',') }} DZD</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span style="color:#666;">المجموع الفرعي:</span>
            <span>{{ number_format($order->subtotal, 0, '.', ',') }} DZD</span>
        </div>
        <div class="total-row">
            <span style="color:#666;">رسوم الشحن:</span>
            <span>{{ number_format($order->shipping, 0, '.', ',') }} DZD</span>
        </div>
        <div class="total-row grand">
            <span>المبلغ الإجمالي:</span>
            <span>{{ number_format($order->total, 0, '.', ',') }} DZD</span>
        </div>
    </div>

    <div><span class="cod">💵 الدفع عند الاستلام (COD)</span></div>
</div>

<div class="sig-box">
    <div class="sig-area">توقيع المرسِل</div>
    <div class="sig-area">توقيع المستلِم</div>
</div>

<div class="footer">ورشة يحيى للموس البوسعادي &mdash; شكراً لثقتك بنا ❤️</div>

<script>window.onload = function() { window.print(); };</script>
</body>
</html>
