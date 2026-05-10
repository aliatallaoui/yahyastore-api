@extends('admin.layout')

@section('title', 'المنتجات')
@section('page-title', 'المنتجات')

@section('topbar-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> منتج جديد
    </a>
@endsection

@section('content')

{{-- Filters --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:14px 20px;">
        <form method="GET" action="{{ route('admin.products.index') }}"
              style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <input type="text" name="q" value="{{ $search ?? '' }}"
                   placeholder="بحث باسم المنتج..." style="min-width:200px;">
            <select name="category" style="min-width:140px;">
                <option value="">كل الفئات</option>
                @foreach($categories as $val => $label)
                <option value="{{ $val }}" @selected(($categoryFilter ?? '') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="active" style="min-width:120px;">
                <option value="">كل الحالات</option>
                <option value="1" @selected(($activeFilter ?? '') === '1')>نشط</option>
                <option value="0" @selected(($activeFilter ?? '') === '0')>مخفي</option>
            </select>
            <select name="stock" style="min-width:140px;">
                <option value="">كل المخزون</option>
                <option value="low" @selected(($stockFilter ?? '') === 'low')>مخزون منخفض</option>
                <option value="out" @selected(($stockFilter ?? '') === 'out')>نفد المخزون</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> فلتر</button>
            @if($search || $categoryFilter || $activeFilter !== null || $stockFilter)
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm">✕ إعادة</a>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>{{ $products->count() }} منتج</h3>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>الاسم</th>
                    <th>الفئة</th>
                    <th>السعر</th>
                    <th>الخصم</th>
                    <th>المخزون</th>
                    <th>الحالة</th>
                    <th>الترتيب</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <img src="{{ url('images/' . $product->image) }}" alt=""
                             style="width:52px;height:40px;object-fit:cover;border-radius:6px;border:1px solid var(--border);"
                             onerror="this.style.opacity='.2'">
                    </td>
                    <td>
                        <div style="font-weight:700;color:var(--text);">{{ $product->name }}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:2px;">{{ Str::limit($product->short_desc, 60) }}</div>
                    </td>
                    <td><span class="badge badge-pending">{{ $product->category_label }}</span></td>
                    <td>
                        <span style="font-weight:700;color:var(--gold);">{{ number_format($product->price) }} DZD</span>
                        @if($product->old_price)
                        <div style="font-size:.75rem;color:var(--text-muted);text-decoration:line-through;">{{ number_format($product->old_price) }}</div>
                        @endif
                    </td>
                    <td>
                        @if($product->discount_percent)
                        <span style="color:var(--danger);font-weight:700;">{{ $product->discount_percent }}%</span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($product->stock === null)
                            <span class="text-muted" style="font-size:.8rem;">—</span>
                        @elseif($product->stock === 0)
                            <span style="color:var(--danger);font-weight:700;font-size:.8rem;">نفد</span>
                        @elseif($product->stock <= 5)
                            <span style="color:var(--warning);font-weight:700;">{{ $product->stock }}</span>
                        @else
                            <span style="color:var(--success);font-weight:700;">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.products.toggle', $product) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm"
                                    style="{{ $product->active ? 'color:var(--success);border-color:var(--success);' : 'color:var(--danger);border-color:var(--danger);' }}">
                                <i class="fas {{ $product->active ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                {{ $product->active ? 'نشط' : 'مخفي' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-muted" style="font-size:.85rem;">{{ $product->sort_order }}</td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  onsubmit="return confirm('حذف {{ addslashes($product->name) }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">لا توجد منتجات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
