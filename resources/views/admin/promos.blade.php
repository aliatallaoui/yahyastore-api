@extends('admin.layout')
@section('title', 'أكواد الخصم')
@section('page-title', 'أكواد الخصم')

@section('content')
<div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

    {{-- Code list --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-tag" style="color:var(--gold);margin-left:8px;"></i> الأكواد ({{ $promos->count() }})</h3>
        </div>
        @if($promos->isEmpty())
        <div class="card-body" style="text-align:center;padding:48px;color:var(--text-muted);">
            <i class="fas fa-tags" style="font-size:2rem;opacity:.3;display:block;margin-bottom:12px;"></i>
            لا توجد أكواد بعد
        </div>
        @else
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>النوع</th>
                        <th>القيمة</th>
                        <th>الحد الأدنى</th>
                        <th>الاستخدام</th>
                        <th>الانتهاء</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promos as $promo)
                    <tr>
                        <td><span style="font-family:monospace;font-weight:800;color:var(--gold);font-size:.9rem;letter-spacing:.05em;">{{ $promo->code }}</span></td>
                        <td class="text-muted">{{ $promo->type === 'percent' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</td>
                        <td style="font-weight:700;">
                            {{ $promo->type === 'percent' ? $promo->value . '%' : number_format($promo->value, 0, '.', ',') . ' DZD' }}
                        </td>
                        <td class="text-muted">
                            {{ $promo->min_order ? number_format($promo->min_order, 0, '.', ',') . ' DZD' : '—' }}
                        </td>
                        <td class="text-muted">
                            {{ $promo->used_count }}{{ $promo->max_uses ? ' / ' . $promo->max_uses : '' }}
                        </td>
                        <td class="text-muted" style="font-size:.8rem;">
                            {{ $promo->expires_at ? $promo->expires_at->format('Y/m/d') : '—' }}
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.promos.toggle', $promo) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm"
                                        style="{{ $promo->active ? 'color:var(--success);border-color:var(--success);' : 'color:var(--danger);border-color:var(--danger);' }}">
                                    <i class="fas {{ $promo->active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $promo->active ? 'نشط' : 'معطل' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}"
                                  onsubmit="return confirm('حذف الكود {{ $promo->code }}؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Create form --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-plus" style="color:var(--gold);margin-left:8px;"></i> كود جديد</h3>
        </div>
        <div class="card-body" style="padding:20px;">
            @if($errors->any())
            <div style="background:rgba(231,76,60,.1);border:1px solid rgba(231,76,60,.3);border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:.85rem;color:var(--danger);">
                {{ $errors->first() }}
            </div>
            @endif
            @if(session('success'))
            <div style="background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:.85rem;color:var(--success);">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="{{ route('admin.promos.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label style="font-size:.82rem;color:var(--text-muted);display:block;margin-bottom:5px;">كود الخصم *</label>
                        <input type="text" name="code" value="{{ old('code') }}"
                               placeholder="SUMMER20" maxlength="30"
                               style="width:100%;text-transform:uppercase;font-family:monospace;font-weight:700;letter-spacing:.05em;"
                               required>
                        <div style="font-size:.73rem;color:var(--text-muted);margin-top:4px;">أحرف وأرقام فقط (لا مسافات)</div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label style="font-size:.82rem;color:var(--text-muted);display:block;margin-bottom:5px;">النوع *</label>
                            <select name="type" id="promoType" onchange="updateValueLabel()" required style="width:100%;">
                                <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>نسبة مئوية %</option>
                                <option value="fixed"   {{ old('type') === 'fixed'   ? 'selected' : '' }}>مبلغ ثابت DZD</option>
                            </select>
                        </div>
                        <div>
                            <label id="valueLabel" style="font-size:.82rem;color:var(--text-muted);display:block;margin-bottom:5px;">القيمة (%) *</label>
                            <input type="number" name="value" value="{{ old('value') }}"
                                   placeholder="10" min="1" max="100" required style="width:100%;">
                        </div>
                    </div>

                    <div>
                        <label style="font-size:.82rem;color:var(--text-muted);display:block;margin-bottom:5px;">الحد الأدنى للطلب (DZD)</label>
                        <input type="number" name="min_order" value="{{ old('min_order') }}"
                               placeholder="فارغ = بدون حد أدنى" min="0" style="width:100%;">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label style="font-size:.82rem;color:var(--text-muted);display:block;margin-bottom:5px;">عدد الاستخدامات</label>
                            <input type="number" name="max_uses" value="{{ old('max_uses') }}"
                                   placeholder="∞ بلا حد" min="1" style="width:100%;">
                        </div>
                        <div>
                            <label style="font-size:.82rem;color:var(--text-muted);display:block;margin-bottom:5px;">تاريخ الانتهاء</label>
                            <input type="date" name="expires_at" value="{{ old('expires_at') }}" style="width:100%;">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:4px;">
                        <i class="fas fa-plus"></i> إنشاء الكود
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateValueLabel() {
    const t = document.getElementById('promoType').value;
    const lbl = document.getElementById('valueLabel');
    const inp = lbl.closest('div').querySelector('input');
    if (t === 'percent') {
        lbl.textContent = 'القيمة (%) *';
        inp.max = 100;
        inp.placeholder = '10';
    } else {
        lbl.textContent = 'القيمة (DZD) *';
        inp.removeAttribute('max');
        inp.placeholder = '1000';
    }
}
</script>
@endsection
