<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('active', true)->orderBy('sort_order');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('sort')) {
            match ($request->sort) {
                'price-low'  => $query->orderBy('price', 'asc'),
                'price-high' => $query->orderBy('price', 'desc'),
                'discount'   => $query->orderBy('discount_percent', 'desc'),
                default      => null,
            };
        }

        $products = $query->get();

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        $categories = [
            'all'       => ['label' => 'الكل',         'count' => Product::where('active', true)->count()],
            'bundle'    => ['label' => 'باقات',        'count' => Product::where('active', true)->where('category', 'bundle')->count()],
            'single'    => ['label' => 'قطع فردية',   'count' => Product::where('active', true)->where('category', 'single')->count()],
            'accessory' => ['label' => 'إكسسوارات',   'count' => Product::where('active', true)->where('category', 'accessory')->count()],
        ];

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if (!$product->active) abort(404);

        $related = $product->relatedProducts();

        return view('products.show', compact('product', 'related'));
    }

    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $results = Product::where('active', true)
            ->where('name', 'like', "%{$q}%")
            ->orderBy('sort_order')
            ->get(['id', 'name', 'price', 'slug']);

        return response()->json($results);
    }
}
