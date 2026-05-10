<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ── Auth ──────────────────────────────────────────────────────────────────

    public function loginForm()
    {
        if (session('admin_authenticated')) {
            return redirect()->route('admin.orders');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required']);

        if ($request->password !== config('app.admin_password')) {
            return back()->withErrors(['password' => 'كلمة المرور غير صحيحة']);
        }

        session(['admin_authenticated' => true]);
        return redirect()->route('admin.orders');
    }

    public function logout()
    {
        session()->forget('admin_authenticated');
        return redirect()->route('admin.login');
    }

    // ── Orders ────────────────────────────────────────────────────────────────

    public function orders(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('q');

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

        $orders = $query->paginate(20)->withQueryString();

        $counts = [
            'all'       => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipped'   => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders', compact('orders', 'counts', 'status', 'search'));
    }

    public function orderShow(Order $order)
    {
        $order->load('items');
        return view('admin.order-show', compact('order'));
    }

    public function orderUpdateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function orderDelete(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'تم حذف الطلب');
    }
}
