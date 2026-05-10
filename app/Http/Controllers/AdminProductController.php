<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    private array $categories = [
        'bundle'    => 'أطقم موس',
        'single'    => 'موس فردي',
        'accessory' => 'إكسسوار',
        'sale'      => 'عروض خاصة',
    ];

    public function index()
    {
        $products = Product::orderBy('sort_order')->get();
        return view('admin.products.index', ['products' => $products, 'categories' => $this->categories]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => null, 'categories' => $this->categories]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Product::create($data);
        return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج بنجاح.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product, 'categories' => $this->categories]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);
        $product->update($data);
        return redirect()->route('admin.products.index')->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'تم حذف المنتج.');
    }

    public function toggle(Product $product)
    {
        $product->update(['active' => !$product->active]);
        return back()->with('success', $product->active ? 'تم تفعيل المنتج.' : 'تم إيقاف المنتج.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $slugRule = 'nullable|string|max:220|unique:products,slug' . ($product ? ',' . $product->id : '');

        $data = $request->validate([
            'name'             => 'required|string|max:200',
            'slug'             => $slugRule,
            'description'      => 'nullable|string',
            'short_desc'       => 'nullable|string|max:300',
            'price'            => 'required|integer|min:0',
            'old_price'        => 'nullable|integer|min:0',
            'category'         => 'required|in:bundle,single,accessory,sale',
            'image_file'       => 'nullable|image|max:4096',
            'image'            => 'nullable|string|max:300',
            'features_raw'     => 'nullable|string',
            'gallery_raw'      => 'nullable|string',
            'sort_order'       => 'sometimes|integer|min:0',
            'active'           => 'sometimes|boolean',
        ]);

        // Handle uploaded image file
        unset($data['image_file']);
        if ($request->hasFile('image_file')) {
            $file      = $request->file('image_file');
            $filename  = 'product-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $data['image'] = $filename;
        }

        // Auto-generate slug
        if (empty($data['slug'])) {
            $base = Str::slug($data['name']);
            if (!$base) {
                $base = 'product-' . time();
            }
            $data['slug'] = $base;
        }

        // Category label
        $data['category_label'] = $this->categories[$data['category']] ?? $data['category'];

        // Discount percent
        $price    = (int) $data['price'];
        $oldPrice = isset($data['old_price']) ? (int) $data['old_price'] : 0;
        $data['discount_percent'] = ($oldPrice > $price && $oldPrice > 0)
            ? (int) round((1 - $price / $oldPrice) * 100)
            : 0;

        // Features: one per line → JSON array
        $data['features'] = collect(explode("\n", $data['features_raw'] ?? ''))
            ->map(fn($l) => trim($l))
            ->filter()
            ->values()
            ->all();
        unset($data['features_raw']);

        // Gallery images: one per line → JSON array
        $data['gallery_images'] = collect(explode("\n", $data['gallery_raw'] ?? ''))
            ->map(fn($l) => trim($l))
            ->filter()
            ->values()
            ->all();
        unset($data['gallery_raw']);

        return $data;
    }
}
