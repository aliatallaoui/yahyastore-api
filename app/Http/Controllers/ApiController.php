<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\AnalyticsEvent;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\SupportTicket;
use App\Services\MetaPixelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function products()
    {
        $products = Product::where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'id'                => $p->id,
                'name'              => $p->name,
                'slug'              => $p->slug,
                'description'       => $p->description,
                'short_description' => $p->short_desc,
                'price'             => (int) $p->price,
                'old_price'         => $p->old_price ? (int) $p->old_price : null,
                'discount'          => $p->discount_percent,
                'badge'             => $p->discount_percent ? '-' . $p->discount_percent . '%' : null,
                'category'          => $p->category,
                'category_label'    => $p->category_label,
                'image'             => url('images/' . $p->image),
                'images'            => collect(array_filter(array_merge(
                    [$p->image],
                    $p->gallery_images ?? []
                )))->map(fn($i) => url('images/' . $i))->values()->all(),
                'features'          => $p->features ?? [],
                'in_stock'          => $p->stock === null || $p->stock > 0,
                'stock'             => $p->stock,
            ]);

        return response()->json($products)
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|min:2|max:100',
            'phone'         => ['required', 'regex:/^(05|06|07)\d{8}$/'],
            'second_phone'  => ['nullable', 'regex:/^(05|06|07)\d{8}$/'],
            'wilaya'        => 'required|string|max:100',
            'wilaya_code'   => 'required|string',
            'commune'       => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:500',
            'delivery_type' => 'nullable|in:home,desk',
            'notes'         => 'nullable|string|max:300',
            'shipping_price'=> 'required|integer|min:0',
            'promo_code'    => 'nullable|string|max:30',
            'items'         => 'required|array|min:1',
            'items.*.product_id'   => 'nullable|integer',
            'items.*.product_name' => 'required|string',
            'items.*.unit_price'   => 'required|integer|min:0',
            'items.*.quantity'     => 'required|integer|min:1',
        ]);

        $subtotal    = array_sum(array_map(fn($i) => $i['unit_price'] * $i['quantity'], $data['items']));
        $shipping    = (int) $data['shipping_price'];

        // Promo code
        $promo    = null;
        $discount = 0;
        if (!empty($data['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper($data['promo_code']))->first();
            if ($promo && $promo->isValid($subtotal)) {
                $discount = $promo->discountAmount($subtotal);
            }
        }

        $total       = max(0, $subtotal - $discount) + $shipping;
        $orderNumber = 'YHY-' . strtoupper(substr(uniqid(), -6));

        $order = DB::transaction(function () use ($data, $orderNumber, $subtotal, $shipping, $total, $discount, $promo) {
            $promoNote = $promo ? ' | كود خصم: ' . $promo->code . ' (-' . number_format($discount, 0, '.', ',') . ' DZD)' : '';
            $notes = trim(
                (($data['delivery_type'] ?? '') === 'desk' ? 'التسليم: مكتب البريد. ' : '')
                . ($data['second_phone'] ?? null ? 'هاتف 2: ' . $data['second_phone'] . '. ' : '')
                . ($data['notes'] ?? '')
                . $promoNote
            );

            $order = Order::create([
                'order_number'   => $orderNumber,
                'name'           => $data['customer_name'],
                'phone'          => $data['phone'],
                'wilaya_code'    => (int) $data['wilaya_code'],
                'wilaya_name'    => $data['wilaya'],
                'address'        => ($data['address'] ?? '') . (($data['commune'] ?? null) ? ' — ' . $data['commune'] : ''),
                'notes'          => $notes,
                'subtotal'       => $subtotal,
                'shipping'       => $shipping,
                'total'          => $total,
                'payment_method' => 'COD',
                'status'         => 'pending',
                'whatsapp_url'   => null,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'price'        => $item['unit_price'],
                    'qty'          => $item['quantity'],
                    'subtotal'     => $item['unit_price'] * $item['quantity'],
                ]);
            }

            if ($promo) $promo->incrementUsed();

            // Build WhatsApp URL so admin can contact the customer directly
            $waUrl = $this->buildWhatsAppUrl($order, $data['items'], $subtotal, $shipping, $total, $discount, $promo?->code);
            $order->update(['whatsapp_url' => $waUrl]);

            return $order;
        });

        // Fire server-side Purchase event to Meta Conversions API
        (new MetaPixelService)->purchase(
            orderNumber: $order->order_number,
            total:       $total,
            currency:    'USD',
            items:       $data['items'],
            phone:       $data['phone'],
            clientIp:    $request->ip(),
            userAgent:   $request->userAgent() ?? '',
        );

        return response()->json([
            'success' => true,
            'data'    => [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $total,
                'status'       => 'pending',
            ],
        ])->header('Access-Control-Allow-Origin', '*');
    }

    private function buildWhatsAppUrl(Order $order, array $items, int $subtotal, int $shipping, int $total, int $discount = 0, ?string $promoCode = null): string
    {
        $storeName = config('app.name', 'ورشة يحيى');
        $waNumber  = config('app.store_whatsapp', '213775108618');

        $msg  = "السلام عليكم، طلب جديد من {$storeName}.\n";
        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "📋 *رقم الطلب:* {$order->order_number}\n";
        $msg .= "━━━━━━━━━━━━━━━\n\n";
        $msg .= "*معلومات الزبون:*\n";
        $msg .= "👤 الاسم: {$order->name}\n";
        $msg .= "📱 الهاتف: {$order->phone}\n\n";
        $msg .= "*معلومات التوصيل:*\n";
        $msg .= "📍 الولاية: {$order->wilaya_name}\n";
        $msg .= "🏠 العنوان: {$order->address}\n";
        if ($order->notes) {
            $msg .= "📝 ملاحظات: {$order->notes}\n";
        }
        $msg .= "\n━━━━━━━━━━━━━━━\n";
        $msg .= "*المنتجات:*\n\n";
        foreach ($items as $i => $item) {
            $lineTotal = $item['unit_price'] * $item['quantity'];
            $msg .= ($i + 1) . ". {$item['product_name']}\n";
            $msg .= "   الكمية: {$item['quantity']} | السعر: " . number_format($lineTotal, 0, '.', ',') . " دج\n\n";
        }
        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "*ملخص الطلب:*\n";
        $msg .= "المجموع: " . number_format($subtotal, 0, '.', ',') . " دج\n";
        if ($discount > 0 && $promoCode) {
            $msg .= "خصم ({$promoCode}): -" . number_format($discount, 0, '.', ',') . " دج\n";
        }
        $msg .= "الشحن: " . number_format($shipping, 0, '.', ',') . " دج\n";
        $msg .= "*الإجمالي: " . number_format($total, 0, '.', ',') . " دج*\n";
        $msg .= "\n*طريقة الدفع:* الدفع عند الاستلام (COD)\n";
        $msg .= "━━━━━━━━━━━━━━━";

        return 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($msg);
    }

    public function storeAnalytics(Request $request)
    {
        $allowed = ['pageview', 'product_view', 'add_to_cart', 'checkout_start'];
        $event   = $request->input('event');

        if (!in_array($event, $allowed)) {
            return response()->json(['ok' => false], 400)
                ->header('Access-Control-Allow-Origin', '*');
        }

        AnalyticsEvent::create([
            'event'        => $event,
            'session_id'   => substr((string) $request->input('session_id', ''), 0, 64),
            'page'         => substr((string) $request->input('page', ''), 0, 200),
            'product_id'   => $request->input('product_id'),
            'product_name' => substr((string) $request->input('product_name', ''), 0, 200),
            'ip'           => $request->ip(),
            'ua'           => substr($request->userAgent() ?? '', 0, 300),
        ]);

        return response()->json(['ok' => true])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function saveCart(Request $request)
    {
        $data = $request->validate([
            'session_id' => 'required|string|max:64',
            'phone'      => ['nullable', 'regex:/^(05|06|07)\d{8}$/'],
            'items'      => 'required|array|min:1',
            'total'      => 'required|integer|min:0',
        ]);

        AbandonedCart::updateOrCreate(
            ['session_id' => $data['session_id']],
            [
                'phone' => $data['phone'] ?? null,
                'items' => $data['items'],
                'total' => $data['total'],
            ]
        );

        return response()->json(['ok' => true])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function storeTicket(Request $request)
    {
        $data = $request->validate([
            'name'    => 'nullable|string|max:100',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:200',
            'message' => 'nullable|string|max:2000',
        ]);

        $ticket = SupportTicket::create($data);

        return response()->json(['success' => true, 'id' => $ticket->id])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function checkPromo(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:30',
            'order_total' => 'required|integer|min:0',
        ]);

        $promo = PromoCode::where('code', strtoupper($request->input('code')))->first();

        if (!$promo || !$promo->isValid((int) $request->input('order_total'))) {
            $reason = 'كود الخصم غير صالح أو منتهي الصلاحية';
            if ($promo && $promo->min_order && (int) $request->input('order_total') < $promo->min_order) {
                $reason = 'الحد الأدنى للطلب هو ' . number_format($promo->min_order, 0, '.', ',') . ' DZD';
            }
            return response()->json(['valid' => false, 'message' => $reason])
                ->header('Access-Control-Allow-Origin', '*');
        }

        $subtotal = (int) $request->input('order_total');
        $discount = $promo->discountAmount($subtotal);

        return response()->json([
            'valid'    => true,
            'type'     => $promo->type,
            'value'    => $promo->value,
            'discount' => $discount,
            'label'    => $promo->type === 'percent'
                ? 'خصم ' . $promo->value . '%'
                : 'خصم ' . number_format($promo->value, 0, '.', ',') . ' DZD',
        ])->header('Access-Control-Allow-Origin', '*');
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^(05|06|07)\d{8}$/'],
        ]);

        $phone  = $request->input('phone');
        $orders = Order::with('items')
            ->where('phone', $phone)
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn($o) => [
                'order_number' => $o->order_number,
                'status'       => $o->status,
                'wilaya'       => $o->wilaya_name,
                'total'        => (int) $o->total,
                'date'         => $o->created_at->format('Y/m/d'),
                'items_count'  => $o->items->sum('qty'),
                'items'        => $o->items->map(fn($i) => [
                    'name' => $i->product_name,
                    'qty'  => $i->qty,
                ])->values()->all(),
            ]);

        return response()->json(['orders' => $orders])
            ->header('Access-Control-Allow-Origin', '*');
    }

    public function handleOptions()
    {
        return response('', 204)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, X-API-Key');
    }
}
