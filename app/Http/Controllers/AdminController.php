<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
            return redirect()->route('admin.orders');
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
            return redirect()->intended(route('admin.orders'));
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

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    public function orderDelete(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders')->with('success', 'تم حذف الطلب.');
    }
}
