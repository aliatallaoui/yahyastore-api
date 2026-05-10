<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class AdminPromoController extends Controller
{
    public function index()
    {
        $promos = PromoCode::latest()->get();
        return view('admin.promos', compact('promos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:30|unique:promo_codes,code|regex:/^[A-Z0-9_\-]+$/i',
            'type'      => 'required|in:percent,fixed',
            'value'     => 'required|integer|min:1',
            'min_order' => 'nullable|integer|min:0',
            'max_uses'  => 'nullable|integer|min:1',
            'expires_at'=> 'nullable|date|after:today',
        ]);

        $data['code'] = strtoupper($data['code']);

        if ($data['type'] === 'percent' && $data['value'] > 100) {
            return back()->withErrors(['value' => 'النسبة لا يمكن أن تتجاوز 100%'])->withInput();
        }

        PromoCode::create($data);
        return back()->with('success', 'تم إنشاء كود الخصم بنجاح.');
    }

    public function toggle(PromoCode $promo)
    {
        $promo->update(['active' => !$promo->active]);
        return back()->with('success', $promo->active ? 'تم تفعيل الكود.' : 'تم إيقاف الكود.');
    }

    public function destroy(PromoCode $promo)
    {
        $promo->delete();
        return back()->with('success', 'تم حذف الكود.');
    }
}
