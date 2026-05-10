<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private const WILAYA_SHIPPING = [
        // Zone 1 – 400 DZD
        28 => 400, 17 => 400, 5 => 400, 7 => 400, 34 => 400, 3 => 400, 51 => 400,
        // Zone 2 – 600 DZD
        16 => 600, 9 => 600, 19 => 600, 25 => 600, 6 => 600, 15 => 600, 26 => 600,
        35 => 600, 10 => 600, 43 => 600, 4 => 600, 40 => 600, 12 => 600, 18 => 600,
        36 => 600, 21 => 600, 44 => 600, 42 => 600, 2 => 600, 27 => 600, 38 => 600,
        14 => 600, 20 => 600, 29 => 600, 47 => 600, 39 => 600, 30 => 600, 55 => 600,
        57 => 600, 58 => 600,
        // Zone 3 – 800 DZD
        31 => 800, 13 => 800, 22 => 800, 46 => 800, 48 => 800, 41 => 800, 23 => 800,
        24 => 800, 32 => 800, 45 => 800, 8 => 800, 1 => 800, 37 => 800, 11 => 800,
        33 => 800, 49 => 800, 50 => 800, 52 => 800, 53 => 800, 54 => 800, 56 => 800,
    ];

    private const WILAYA_NAMES = [
        1=>'أدرار',2=>'الشلف',3=>'الأغواط',4=>'أم البواقي',5=>'باتنة',6=>'بجاية',
        7=>'بسكرة',8=>'بشار',9=>'البليدة',10=>'البويرة',11=>'تمنراست',12=>'تبسة',
        13=>'تلمسان',14=>'تيارت',15=>'تيزي وزو',16=>'الجزائر',17=>'الجلفة',
        18=>'جيجل',19=>'سطيف',20=>'سعيدة',21=>'سكيكدة',22=>'سيدي بلعباس',
        23=>'عنابة',24=>'قالمة',25=>'قسنطينة',26=>'المدية',27=>'مستغانم',
        28=>'المسيلة',29=>'معسكر',30=>'ورقلة',31=>'وهران',32=>'البيض',33=>'إليزي',
        34=>'برج بوعريريج',35=>'بومرداس',36=>'الطارف',37=>'تندوف',38=>'تيسمسيلت',
        39=>'الوادي',40=>'خنشلة',41=>'سوق أهراس',42=>'تيبازة',43=>'ميلة',
        44=>'عين الدفلة',45=>'النعامة',46=>'عين تموشنت',47=>'غرداية',48=>'غليزان',
        49=>'تيميمون',50=>'برج باجي مختار',51=>'أولاد جلال',52=>'بني عباس',
        53=>'عين صالح',54=>'عين قزام',55=>'توقرت',56=>'جانت',57=>'المغير',58=>'المنيعة',
    ];

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|min:3|max:100',
            'phone'      => ['required', 'regex:/^(05|06|07)\d{8}$/'],
            'wilaya'     => 'required|integer|between:1,58',
            'address'    => 'required|string|min:5|max:500',
            'notes'      => 'nullable|string|max:300',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return response()->json(['error' => 'السلة فارغة'], 422);
        }

        $wilayaCode  = (int) $request->wilaya;
        $wilayaName  = self::WILAYA_NAMES[$wilayaCode] ?? 'غير معروفة';
        $shipping    = self::WILAYA_SHIPPING[$wilayaCode] ?? 600;
        $subtotal    = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart));
        $total       = $subtotal + $shipping;
        $orderNumber = 'YHY-' . strtoupper(substr(uniqid(), -6));

        $whatsappMsg = $this->buildWhatsappMessage(
            $orderNumber, $request->name, $request->phone,
            $wilayaName, $request->address, $request->notes ?? '',
            $cart, $subtotal, $shipping, $total
        );
        $whatsappUrl = 'https://wa.me/213775108618?text=' . rawurlencode($whatsappMsg);

        DB::transaction(function () use (
            $request, $orderNumber, $wilayaCode, $wilayaName,
            $shipping, $subtotal, $total, $whatsappUrl, $cart
        ) {
            $order = Order::create([
                'order_number'   => $orderNumber,
                'name'           => $request->name,
                'phone'          => $request->phone,
                'wilaya_code'    => $wilayaCode,
                'wilaya_name'    => $wilayaName,
                'address'        => $request->address,
                'notes'          => $request->notes,
                'subtotal'       => $subtotal,
                'shipping'       => $shipping,
                'total'          => $total,
                'payment_method' => 'COD',
                'status'         => 'pending',
                'whatsapp_url'   => $whatsappUrl,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'qty'          => $item['qty'],
                    'subtotal'     => $item['price'] * $item['qty'],
                ]);
            }
        });

        session(['cart' => []]);

        return response()->json([
            'success'      => true,
            'order_number' => $orderNumber,
            'whatsapp_url' => $whatsappUrl,
            'subtotal'     => $subtotal,
            'shipping'     => $shipping,
            'total'        => $total,
            'wilaya_name'  => $wilayaName,
        ]);
    }

    private function buildWhatsappMessage(
        string $orderNumber, string $name, string $phone,
        string $wilayaName, string $address, string $notes,
        array $cart, int $subtotal, int $shipping, int $total
    ): string {
        $msg  = "🔪 *طلب جديد - ورشة يحيى* 🔪\n";
        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "📋 *رقم الطلب:* {$orderNumber}\n";
        $msg .= "━━━━━━━━━━━━━━━\n\n";
        $msg .= "👤 *الاسم:* {$name}\n";
        $msg .= "📱 *الهاتف:* {$phone}\n";
        $msg .= "📍 *الولاية:* {$wilayaName}\n";
        $msg .= "🏠 *العنوان:* {$address}\n";
        if ($notes) $msg .= "📝 *ملاحظات:* {$notes}\n";
        $msg .= "\n━━━━━━━━━━━━━━━\n";
        $msg .= "🛒 *المنتجات:*\n\n";
        foreach ($cart as $item) {
            $lineTotal = number_format($item['price'] * $item['qty'], 0, '.', ',');
            $msg .= "▸ {$item['name']}\n";
            $msg .= "  العدد: {$item['qty']} | السعر: {$lineTotal} DZD\n\n";
        }
        $msg .= "━━━━━━━━━━━━━━━\n";
        $msg .= "💰 *المجموع الفرعي:* " . number_format($subtotal, 0, '.', ',') . " DZD\n";
        $msg .= "🚚 *التوصيل:* " . number_format($shipping, 0, '.', ',') . " DZD\n";
        $msg .= "✅ *المجموع الكلي:* " . number_format($total, 0, '.', ',') . " DZD\n";
        $msg .= "💳 *طريقة الدفع:* الدفع عند الاستلام\n";
        $msg .= "━━━━━━━━━━━━━━━";
        return $msg;
    }
}
