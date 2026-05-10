<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    public function index()
    {
        return response()->json($this->getCart());
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);
        $qty = $request->integer('qty', 1);

        $cart = $this->getCart();
        $key = "p{$product->id}";

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'qty'   => $qty,
            ];
        }

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'cart'    => array_values($cart),
            'count'   => array_sum(array_column($cart, 'qty')),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'qty'        => 'required|integer|min:0|max:99',
        ]);

        $cart = $this->getCart();
        $key  = "p{$request->product_id}";

        if ($request->qty <= 0) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['qty'] = $request->qty;
        }

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'cart'    => array_values($cart),
            'count'   => array_sum(array_column($cart, 'qty')),
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        $cart = $this->getCart();
        unset($cart["p{$request->product_id}"]);
        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'cart'    => array_values($cart),
            'count'   => array_sum(array_column($cart, 'qty')),
        ]);
    }

    public function clear()
    {
        $this->saveCart([]);
        return response()->json(['success' => true, 'cart' => [], 'count' => 0]);
    }
}
