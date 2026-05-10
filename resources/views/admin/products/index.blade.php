@extends('admin.layout')

@section('title', 'المنتجات')
@section('page-title', 'المنتجات')

@section('topbar-actions')
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> منتج جديد
    </a>
@endsection

@section('content')

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
