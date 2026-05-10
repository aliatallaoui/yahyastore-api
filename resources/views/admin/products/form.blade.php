@extends('admin.layout')

@section('title', $product ? 'تعديل: ' . $product->name : 'منتج جديد')
@section('page-title', $product ? 'تعديل المنتج' : 'منتج جديد')

@section('topbar-actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm">← العودة</a>
@endsection

@section('content')

@php
$isEdit      = (bool) $product;
$action      = $isEdit ? route('admin.products.update', $product) : route('admin.products.store');
$method      = $isEdit ? 'PUT' : 'POST';
$featuresRaw = $isEdit ? implode("\n", $product->features ?? []) : '';
$galleryRaw  = $isEdit ? implode("\n", $product->gallery_images ?? []) : '';
@endphp

<style>
.field-label { display:block; font-size:.85rem; font-weight:700; margin-bottom:6px; color:var(--text); }
.field-hint  { font-size:.75rem; color:var(--text-muted); margin-top:4px; }
.field-error { color:var(--danger); font-size:.8rem; margin-top:4px; }
.form-control { width:100%; background:var(--bg); border:1px solid var(--border); border-radius:8px; color:var(--text); font-family:inherit; font-size:.9rem; padding:9px 14px; outline:none; transition:border-color .2s; }
.form-control:focus { border-color:var(--gold); }
.form-control.is-error { border-color:var(--danger); }
.card-title { font-size:.95rem; font-weight:800; color:var(--gold); margin-bottom:18px; }
.card { padding:20px; margin-bottom:16px; }
.upload-zone {
    border: 2px dashed var(--border); border-radius: 10px; padding: 28px 16px;
    text-align: center; cursor: pointer; transition: border-color .2s, background .2s;
    background: var(--bg);
}
.upload-zone:hover { border-color: var(--gold); background: rgba(212,175,55,.04); }
</style>

<div style="display:grid;grid-template-columns:1fr 300px;gap:20px;align-items:start;">

    <div>
        <div class="card">
            <div class="card-title">معلومات المنتج</div>
            <form method="POST" action="{{ $action }}" id="productForm" enctype="multipart/form-data">
                @csrf
                @method($method)

                <div style="margin-bottom:16px;">
                    <label class="field-label">اسم المنتج <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-error' : '' }}"
                           value="{{ old('name', $product?->name) }}" required oninput="autoSlug(this.value)">
                    @error('name') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label class="field-label">الـ Slug (يُملأ تلقائياً)</label>
                    <input type="text" name="slug" id="slugInput" class="form-control {{ $errors->has('slug') ? 'is-error' : '' }}"
                           value="{{ old('slug', $product?->slug) }}" dir="ltr" placeholder="اتركه فارغاً للتوليد التلقائي">
                    @error('slug') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label class="field-label">وصف قصير (للبطاقة)</label>
                    <input type="text" name="short_desc" class="form-control" maxlength="300"
                           value="{{ old('short_desc', $product?->short_desc) }}"
                           placeholder="جملة أو جملتان تظهر على بطاقة المنتج">
                    <div class="field-hint">يظهر على بطاقة المنتج في قائمة المنتجات</div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="field-label">الوصف الكامل</label>
                    <textarea name="description" class="form-control" rows="5"
                              placeholder="وصف تفصيلي يظهر في صفحة المنتج...">{{ old('description', $product?->description) }}</textarea>
                    <div class="field-hint">يظهر في صفحة تفاصيل المنتج في المتجر</div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                    <div>
                        <label class="field-label">السعر (DZD) <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="price" class="form-control {{ $errors->has('price') ? 'is-error' : '' }}"
                               min="0" value="{{ old('price', $product?->price) }}" required oninput="calcDiscount()">
                        @error('price') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="field-label">السعر القديم (DZD)</label>
                        <input type="number" name="old_price" class="form-control" min="0"
                               value="{{ old('old_price', $product?->old_price) }}"
                               placeholder="اتركه فارغاً إذا لا يوجد خصم" oninput="calcDiscount()">
                        <div class="field-hint" id="discountHint">
                            @if($product?->discount_percent) الخصم الحالي: {{ $product->discount_percent }}% @endif
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                    <div>
                        <label class="field-label">الفئة <span style="color:var(--danger)">*</span></label>
                        <select name="category" class="form-control">
                            @foreach($categories as $val => $label)
                                <option value="{{ $val }}" @selected(old('category', $product?->category) === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" min="0"
                               value="{{ old('sort_order', $product?->sort_order ?? 0) }}">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="field-label">صورة المنتج الرئيسية</label>
                    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('imageFileInput').click()">
                        <i class="fas fa-cloud-upload-alt" style="font-size:1.6rem;color:var(--gold);margin-bottom:6px;display:block;"></i>
                        <div style="font-size:.85rem;color:var(--text-muted);">انقر لرفع صورة <span style="opacity:.6;">(JPG, PNG, WebP — حتى 4MB)</span></div>
                        <div id="uploadFileName" style="font-size:.8rem;color:var(--gold);margin-top:6px;"></div>
                    </div>
                    <input type="file" name="image_file" id="imageFileInput" accept="image/*"
                           style="display:none;" onchange="handleImageUpload(this)">
                    <input type="text" name="image" class="form-control" id="imageInput"
                           value="{{ old('image', $product?->image) }}"
                           placeholder="أو اكتب اسم الملف يدوياً: product-1.jpg" dir="ltr"
                           style="margin-top:8px;" oninput="previewImage(this.value)">
                    <div class="field-hint">رفع ملف جديد يستبدل الاسم المكتوب تلقائياً</div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="field-label">معرض الصور (مسار واحد في كل سطر)</label>
                    <textarea name="gallery_raw" class="form-control" rows="4" dir="ltr"
                              placeholder="product-5.jpg&#10;gallery-4.jpg">{{ old('gallery_raw', $galleryRaw) }}</textarea>
                </div>

                <div style="margin-bottom:24px;">
                    <label class="field-label">مميزات المنتج (ميزة واحدة في كل سطر)</label>
                    <textarea name="features_raw" class="form-control" rows="6"
                              placeholder="مقبض من خشب الشريمة&#10;صناعة يدوية 100%&#10;مع غمد جلدي">{{ old('features_raw', $featuresRaw) }}</textarea>
                    <div class="field-hint">تظهر كقائمة نقاط في صفحة المنتج</div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:13px;">
                    <i class="fas fa-save"></i> {{ $isEdit ? 'حفظ التعديلات' : 'إضافة المنتج' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Sidebar --}}
    <div>
        <div class="card" style="text-align:center;">
            <div class="card-title">معاينة الصورة</div>
            <img id="imagePreview"
                 src="{{ $product?->image ? url('images/' . $product->image) : '' }}"
                 alt="معاينة"
                 style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;border:1px solid var(--border);"
                 onerror="this.style.opacity='.2'">
        </div>

        <div class="card">
            <div class="card-title">الإعدادات</div>
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:.9rem;">
                <input type="hidden" name="active" value="0" form="productForm">
                <input type="checkbox" name="active" value="1" form="productForm"
                       style="width:18px;height:18px;cursor:pointer;"
                       {{ old('active', $product?->active ?? true) ? 'checked' : '' }}>
                <span>منتج نشط (يظهر في المتجر)</span>
            </label>
        </div>

        @if($product)
        <div class="card">
            <div class="card-title" style="font-size:.8rem;color:var(--text-muted);">بيانات المنتج</div>
            <div style="font-size:.82rem;line-height:2;">
                <div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border);padding:4px 0;">
                    <span class="text-muted">ID</span>
                    <span style="font-family:monospace;">{{ $product->id }}</span>
                </div>
                @if($product->discount_percent)
                <div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border);padding:4px 0;">
                    <span class="text-muted">الخصم</span>
                    <span style="color:var(--danger);font-weight:700;">{{ $product->discount_percent }}%</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;padding:4px 0;">
                    <span class="text-muted">آخر تحديث</span>
                    <span>{{ $product->updated_at->format('d/m H:i') }}</span>
                </div>
            </div>
        </div>
        @endif

        @if($isEdit)
        <div class="card" style="border:1px solid rgba(231,76,60,.4);">
            <div class="card-title" style="color:var(--danger);">منطقة الخطر</div>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج نهائيًا؟')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width:100%;">
                    <i class="fas fa-trash"></i> حذف المنتج نهائيًا
                </button>
            </form>
        </div>
        @endif
    </div>

</div>

<script>
function previewImage(src) {
    const img = document.getElementById('imagePreview');
    img.style.opacity = '1';
    img.src = src.startsWith('http') ? src : (src ? '/images/' + src.trim() : '');
    img.onerror = () => img.style.opacity = '.2';
}

function autoSlug(name) {
    const s = document.getElementById('slugInput');
    if (s.dataset.manual) return;
    s.value = name.toLowerCase().trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9؀-ۿ\-]/g, '');
}
document.getElementById('slugInput')?.addEventListener('input', function() {
    this.dataset.manual = '1';
});

function calcDiscount() {
    const price    = parseInt(document.querySelector('[name=price]').value) || 0;
    const oldPrice = parseInt(document.querySelector('[name=old_price]').value) || 0;
    const hint     = document.getElementById('discountHint');
    if (oldPrice > price && oldPrice > 0) {
        const pct = Math.round((1 - price / oldPrice) * 100);
        hint.textContent = 'الخصم: ' + pct + '%';
        hint.style.color = 'var(--gold)';
    } else {
        hint.textContent = '';
    }
}

function updateCategoryLabel(val) {
    // Label is computed server-side; just visual feedback
}

function handleImageUpload(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    document.getElementById('uploadFileName').textContent = file.name;
    document.getElementById('uploadZone').style.borderColor = 'var(--gold)';
    // Preview
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('imagePreview');
        img.style.opacity = '1';
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
    // Clear manual input since file takes priority
    document.getElementById('imageInput').value = '';
}
</script>

@endsection
