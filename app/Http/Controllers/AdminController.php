<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\AnalyticsEvent;
use App\Models\Order;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    // ── Auth ──────────────────────────────────────────────────────────────────

    public function loginForm()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $key = 'admin-login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "محاولات كثيرة جداً. حاول مجدداً بعد {$seconds} ثانية.",
            ]);
        }

        $credentials = ['email' => $request->email, 'password' => $request->password, 'is_admin' => true];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($key, 300);

        throw ValidationException::withMessages([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard()
    {
        // Abandoned carts: last 7 days, has phone, no matching order placed
        $recentCarts = AbandonedCart::where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('phone')->get();
        $cartPhones   = $recentCarts->pluck('phone')->unique()->values()->all();
        $orderedPhones = Order::whereIn('phone', $cartPhones)->pluck('phone')->flip()->all();
        $abandonedCount = $recentCarts->filter(fn($c) => !isset($orderedPhones[$c->phone])
            && $c->created_at->lt(now()->subMinutes(30))
        )->count();

        // Low stock: products where stock is set and <= 5
        $lowStock = \App\Models\Product::where('active', true)
            ->whereNotNull('stock')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->get(['id', 'name', 'stock']);

        $stats = [
            'orders_total'    => Order::count(),
            'orders_today'    => Order::whereDate('created_at', today())->count(),
            'orders_pending'  => Order::where('status', 'pending')->count(),
            'revenue_total'   => Order::whereIn('status', ['confirmed','shipped','delivered'])->sum('total'),
            'tickets_new'     => SupportTicket::where('status', 'new')->count(),
            'carts_abandoned' => $abandonedCount,
        ];

        $recent = Order::with('items')->latest()->limit(8)->get();

        // Top 5 selling products (all time)
        $topProducts = \App\Models\OrderItem::selectRaw("product_name, SUM(qty) as total_qty, SUM(subtotal) as total_revenue")
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Sale end countdown (launch sale ends 2026-05-17)
        $saleEnd      = \Carbon\Carbon::parse('2026-05-17 23:59:59');
        $saleActive   = now()->lt($saleEnd);
        $saleDaysLeft = $saleActive ? (int) ceil(now()->diffInHours($saleEnd) / 24) : 0;

        return view('admin.dashboard', compact('stats', 'recent', 'lowStock', 'topProducts', 'saleActive', 'saleDaysLeft'));
    }

    // ── Profile / Change password ─────────────────────────────────────────────

    public function profileForm()
    {
        return view('admin.profile');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.']);
        }

        Auth::user()->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }

    public function changeName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Auth::user()->update(['name' => $request->name]);
        return back()->with('success', 'تم تحديث الاسم بنجاح.');
    }

    // ── Orders ────────────────────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $status    = $request->query('status');
        $search    = $request->query('q');
        $dateFrom  = $request->query('date_from');
        $dateTo    = $request->query('date_to');

        $query = Order::with('items')->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'all'       => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipped'   => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders', compact('orders', 'counts', 'status', 'search', 'dateFrom', 'dateTo'));
    }

    public function orderShow(Order $order)
    {
        $order->load('items');
        return view('admin.order-show', compact('order'));
    }

    public function orderPrint(Order $order)
    {
        $order->load('items');
        return view('admin.order-print', compact('order'));
    }

    public function orderUpdateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    public function orderDelete(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'تم حذف الطلب.');
    }

    // ── Orders export (CSV) ───────────────────────────────────────────────────

    public function exportOrders(Request $request)
    {
        $status   = $request->query('status');
        $search   = $request->query('q');
        $dateFrom = $request->query('date_from');
        $dateTo   = $request->query('date_to');

        $query = Order::with('items')->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->get();

        $statusLabels = [
            'pending'   => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'shipped'   => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
        ];

        $rows   = [];
        $rows[] = ['رقم الطلب','التاريخ','الاسم','الهاتف','كود الولاية','الولاية','العنوان','المنتجات','مجموع المنتجات','الشحن','الإجمالي','الحالة','ملاحظات'];

        foreach ($orders as $order) {
            $itemsSummary = $order->items->map(fn($i) => "{$i->product_name} x{$i->qty}")->implode(' | ');
            $rows[] = [
                $order->order_number,
                $order->created_at->format('Y-m-d H:i'),
                $order->name,
                $order->phone,
                $order->wilaya_code,
                $order->wilaya_name,
                $order->address,
                $itemsSummary,
                $order->subtotal,
                $order->shipping,
                $order->total,
                $statusLabels[$order->status] ?? $order->status,
                $order->notes,
            ];
        }

        $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel Arabic support
            fputs($handle, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Bulk order status update ───────────────────────────────────────────────

    public function bulkUpdateOrders(Request $request)
    {
        $request->validate([
            'order_ids'   => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
            'status'      => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        Order::whereIn('id', $request->order_ids)
             ->update(['status' => $request->status]);

        $count = count($request->order_ids);
        return back()->with('success', "تم تحديث {$count} طلب بنجاح.");
    }

    // ── Order notification polling ────────────────────────────────────────────

    public function latestOrderId()
    {
        $latest = Order::latest()->value('id');
        return response()->json(['id' => $latest ?? 0]);
    }

    // ── Real-time order stream (Server-Sent Events) ───────────────────────────

    public function streamOrders(Request $request)
    {
        session()->save();
        $initialId = (int) $request->header('Last-Event-ID', 0);

        return response()->stream(function () use ($initialId) {
            @set_time_limit(120);

            $lastId    = $initialId ?: (Order::latest()->value('id') ?? 0);
            $startTime = time();

            echo "id: {$lastId}\n";
            echo "data: " . json_encode(['id' => $lastId, 'init' => true]) . "\n\n";
            if (ob_get_level()) ob_flush();
            flush();

            while (!connection_aborted() && (time() - $startTime) < 90) {
                sleep(4);
                if (connection_aborted()) break;

                $latestId = Order::latest()->value('id') ?? 0;

                if ($latestId > $lastId) {
                    $lastId = $latestId;
                    echo "id: {$lastId}\n";
                    echo "data: " . json_encode(['id' => $latestId]) . "\n\n";
                } else {
                    echo ": ping\n\n";
                }

                if (ob_get_level()) ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'Connection'        => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    // ── Analytics ─────────────────────────────────────────────────────────────

    public function analytics()
    {
        $now   = now();
        $today = $now->copy()->startOfDay();
        $d7    = $now->copy()->subDays(7)->startOfDay();
        $d30   = $now->copy()->subDays(30)->startOfDay();
        $d14   = $now->copy()->subDays(14)->startOfDay();

        // ── KPIs ──
        $kpi = [
            'visits_today'    => AnalyticsEvent::where('event', 'pageview')->where('created_at', '>=', $today)->count(),
            'unique_today'    => AnalyticsEvent::where('event', 'pageview')->where('created_at', '>=', $today)->distinct('session_id')->count('session_id'),
            'visits_30'       => AnalyticsEvent::where('event', 'pageview')->where('created_at', '>=', $d30)->count(),
            'unique_30'       => AnalyticsEvent::where('event', 'pageview')->where('created_at', '>=', $d30)->distinct('session_id')->count('session_id'),
            'product_views_30'=> AnalyticsEvent::where('event', 'product_view')->where('created_at', '>=', $d30)->count(),
            'add_to_cart_30'  => AnalyticsEvent::where('event', 'add_to_cart')->where('created_at', '>=', $d30)->count(),
            'checkout_30'     => AnalyticsEvent::where('event', 'checkout_start')->where('created_at', '>=', $d30)->count(),
            'orders_30'       => Order::where('created_at', '>=', $d30)->count(),
            'orders_today'    => Order::whereDate('created_at', today())->count(),
            'revenue_30'      => Order::where('created_at', '>=', $d30)->whereIn('status', ['confirmed','shipped','delivered'])->sum('total'),
            'revenue_today'   => Order::whereDate('created_at', today())->whereIn('status', ['confirmed','shipped','delivered'])->sum('total'),
        ];

        // ── Conversion funnel (last 30 days, unique sessions) ──
        $funnel = [
            'visits'   => $kpi['unique_30'],
            'views'    => AnalyticsEvent::where('event', 'product_view')->where('created_at', '>=', $d30)->distinct('session_id')->count('session_id'),
            'cart'     => AnalyticsEvent::where('event', 'add_to_cart')->where('created_at', '>=', $d30)->distinct('session_id')->count('session_id'),
            'checkout' => AnalyticsEvent::where('event', 'checkout_start')->where('created_at', '>=', $d30)->distinct('session_id')->count('session_id'),
            'orders'   => $kpi['orders_30'],
        ];

        // ── 14-day chart ──
        $chartLabels = [];
        $chartVisits = [];
        $chartOrders = [];

        $dailyVisitsRaw = AnalyticsEvent::where('event', 'pageview')
            ->where('created_at', '>=', $d14)
            ->selectRaw("DATE(created_at) as date, COUNT(DISTINCT session_id) as cnt")
            ->groupBy('date')
            ->pluck('cnt', 'date');

        $dailyOrdersRaw = Order::where('created_at', '>=', $d14)
            ->selectRaw("DATE(created_at) as date, COUNT(*) as cnt")
            ->groupBy('date')
            ->pluck('cnt', 'date');

        for ($i = 13; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->format('Y-m-d');
            $chartLabels[] = $now->copy()->subDays($i)->format('d/m');
            $chartVisits[] = (int) ($dailyVisitsRaw[$d] ?? 0);
            $chartOrders[] = (int) ($dailyOrdersRaw[$d] ?? 0);
        }

        // ── Top pages ──
        $topPages = AnalyticsEvent::where('event', 'pageview')
            ->where('created_at', '>=', $d30)
            ->selectRaw("page, COUNT(*) as views")
            ->groupBy('page')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // ── Top products viewed ──
        $topViewed = AnalyticsEvent::where('event', 'product_view')
            ->where('created_at', '>=', $d30)
            ->selectRaw("product_name, COUNT(*) as views")
            ->groupBy('product_name')
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        // ── Top products added to cart ──
        $topCart = AnalyticsEvent::where('event', 'add_to_cart')
            ->where('created_at', '>=', $d30)
            ->selectRaw("product_name, COUNT(*) as adds")
            ->groupBy('product_name')
            ->orderByDesc('adds')
            ->limit(8)
            ->get();

        // ── Orders by wilaya (top 10) ──
        $byWilaya = Order::where('created_at', '>=', $d30)
            ->selectRaw("wilaya_name, COUNT(*) as cnt, SUM(total) as revenue")
            ->groupBy('wilaya_name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // ── Device split (30d) ──
        $mobilePatterns = '%Mobile%';
        $mobileCount = AnalyticsEvent::where('event', 'pageview')
            ->where('created_at', '>=', $d30)
            ->where(fn($q) => $q->where('ua','like','%Mobile%')->orWhere('ua','like','%Android%')->orWhere('ua','like','%iPhone%')->orWhere('ua','like','%iPad%'))
            ->distinct('session_id')->count('session_id');
        $desktopCount = max(0, $kpi['unique_30'] - $mobileCount);

        // ── Recent events ──
        $recentEvents = AnalyticsEvent::latest('created_at')->limit(15)->get();

        return view('admin.analytics', compact(
            'kpi','funnel','chartLabels','chartVisits','chartOrders',
            'topPages','topViewed','topCart','byWilaya',
            'mobileCount','desktopCount','recentEvents'
        ));
    }

    // ── Support Tickets ───────────────────────────────────────────────────────

    public function tickets(Request $request)
    {
        $status = $request->query('status');

        $query = SupportTicket::latest();
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $tickets = $query->paginate(20)->withQueryString();

        $counts = [
            'all'     => SupportTicket::count(),
            'new'     => SupportTicket::where('status', 'new')->count(),
            'read'    => SupportTicket::where('status', 'read')->count(),
            'replied' => SupportTicket::where('status', 'replied')->count(),
        ];

        return view('admin.tickets', compact('tickets', 'counts', 'status'));
    }

    public function ticketShow(SupportTicket $ticket)
    {
        if ($ticket->status === 'new') {
            $ticket->update(['status' => 'read']);
        }
        return view('admin.ticket-show', compact('ticket'));
    }

    public function ticketUpdateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => 'required|in:new,read,replied']);
        $ticket->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث الحالة.');
    }

    public function ticketDelete(SupportTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets')->with('success', 'تم حذف التذكرة.');
    }

    // ── Abandoned Carts ───────────────────────────────────────────────────────

    public function carts()
    {
        $carts = AbandonedCart::latest()
            ->where('created_at', '>=', now()->subDays(7))
            ->paginate(30);

        // Find which phones have placed orders (= recovered carts)
        $phones = $carts->pluck('phone')->filter()->unique()->values()->all();
        $recoveredPhones = Order::whereIn('phone', $phones)->pluck('phone')->flip()->all();

        return view('admin.carts', compact('carts', 'recoveredPhones'));
    }

    public function cartDelete(AbandonedCart $cart)
    {
        $cart->delete();
        return back()->with('success', 'تم حذف السجل.');
    }

    // ── Customers ─────────────────────────────────────────────────────────────

    public function customers(Request $request)
    {
        $search = $request->query('q');

        $query = \Illuminate\Support\Facades\DB::table('orders')
            ->selectRaw('
                phone,
                MAX(name)           as name,
                COUNT(*)            as orders_count,
                SUM(total)          as total_spent,
                MAX(created_at)     as last_order_at,
                MAX(wilaya_name)    as wilaya
            ')
            ->groupBy('phone');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderByDesc('last_order_at')->paginate(30)->withQueryString();

        return view('admin.customers', compact('customers', 'search'));
    }
}
