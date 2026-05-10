@extends('admin.layout')

@section('title', 'الطلبات')
@section('page-title', 'الطلبات')

@section('topbar-actions')
<div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.orders') }}" class="search-form">
        @if($status && $status !== 'all')
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <input type="text" name="q" placeholder="بحث بالاسم، الهاتف، رقم الطلب..."
               value="{{ $search ?? '' }}" style="min-width:220px;">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
        @if($search)
        <a href="{{ route('admin.orders', $status ? ['status' => $status] : []) }}" class="btn btn-outline btn-sm">✕</a>
        @endif
    </form>

    {{-- CSV Export --}}
    <a href="{{ route('admin.orders.export', array_filter(['status' => $status, 'q' => $search])) }}"
       class="btn btn-outline btn-sm" title="تصدير الطلبات الحالية إلى CSV">
        <i class="fas fa-download"></i> تصدير CSV
    </a>
</div>
@endsection

@section('content')

@php
$statusLabels = ['all'=>'الكل','pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'];
$statusColors = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','shipped'=>'badge-shipped','delivered'=>'badge-delivered','cancelled'=>'badge-cancelled'];
@endphp

{{-- Stats tabs --}}
<div class="stats-row" style="margin-bottom:16px;">
    @foreach ($counts as $key => $count)
    <a href="{{ route('admin.orders', ['status' => $key === 'all' ? null : $key]) }}" style="text-decoration:none;">
        <div class="stat-card {{ ($status ?? 'all') === $key ? 'active' : '' }}">
            <div class="val">{{ $count }}</div>
            <div class="lbl">{{ $statusLabels[$key] }}</div>
        </div>
    </a>
    @endforeach
</div>

{{-- Bulk action bar (hidden until checkboxes selected) --}}
<div id="bulkBar" style="
    display:none;position:sticky;top:0;z-index:100;
    background:#1a1a10;border:1px solid var(--gold);border-radius:var(--radius);
    padding:12px 18px;margin-bottom:14px;
    display:none;align-items:center;gap:12px;flex-wrap:wrap;
    box-shadow:0 4px 20px rgba(0,0,0,.5);
">
    <span id="bulkCount" style="color:var(--gold);font-weight:800;font-size:.9rem;"></span>
    <span style="color:var(--text-muted);font-size:.85rem;">طلب محدد</span>
    <form id="bulkForm" method="POST" action="{{ route('admin.orders.bulk-status') }}" style="display:flex;gap:8px;align-items:center;margin-right:auto;">
        @csrf
        <div id="bulkHiddenInputs"></div>
        <select name="status" style="padding:6px 12px;font-size:.85rem;border-radius:8px;background:var(--bg);border:1px solid var(--border);color:var(--text);font-family:inherit;">
            <option value="">— اختر الحالة الجديدة —</option>
            <option value="confirmed">مؤكد</option>
            <option value="shipped">تم الشحن</option>
            <option value="delivered">تم التسليم</option>
            <option value="cancelled">ملغي</option>
            <option value="pending">قيد الانتظار</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm" onclick="return confirmBulk(this)">
            <i class="fas fa-check"></i> تطبيق
        </button>
    </form>
    <button onclick="clearSelection()" class="btn btn-outline btn-sm">إلغاء التحديد</button>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-shopping-bag" style="color:var(--gold);margin-left:8px;"></i> قائمة الطلبات</h3>
        <span class="text-muted" style="font-size:.82rem;">{{ $orders->total() }} طلب</span>
    </div>

    @if ($orders->isEmpty())
    <div class="card-body" style="text-align:center;padding:48px;color:var(--text-muted);">
        <i class="fas fa-inbox" style="font-size:2.5rem;margin-bottom:14px;display:block;opacity:.3;"></i>
        لا توجد طلبات
    </div>
    @else
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th style="width:36px;">
                        <input type="checkbox" id="selectAll" title="تحديد الكل"
                               style="width:16px;height:16px;cursor:pointer;accent-color:var(--gold);">
                    </th>
                    <th>رقم الطلب</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>الولاية</th>
                    <th>الإجمالي</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr data-id="{{ $order->id }}" class="order-row">
                    <td>
                        <input type="checkbox" class="order-cb" value="{{ $order->id }}"
                               style="width:16px;height:16px;cursor:pointer;accent-color:var(--gold);">
                    </td>
                    <td>
                        <span style="font-family:monospace;color:var(--gold);font-size:.82rem;font-weight:700;">
                            {{ $order->order_number }}
                        </span>
                    </td>
                    <td style="font-weight:600;">{{ $order->name }}</td>
                    <td><span style="direction:ltr;display:inline-block;font-size:.88rem;">{{ $order->phone }}</span></td>
                    <td class="text-muted">{{ $order->wilaya_name }}</td>
                    <td style="font-weight:700;color:var(--gold);white-space:nowrap;">
                        {{ number_format($order->total, 0, '.', ',') }} DZD
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="inline-status-form">
                            @csrf
                            <select name="status" class="status-select status-{{ $order->status }}"
                                    onchange="this.className='status-select status-'+this.value; this.closest('form').submit()">
                                @foreach (['pending'=>'قيد الانتظار','confirmed'=>'مؤكد','shipped'=>'تم الشحن','delivered'=>'تم التسليم','cancelled'=>'ملغي'] as $val => $lbl)
                                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                    <td class="text-muted" style="font-size:.78rem;white-space:nowrap;">
                        <div>{{ $order->created_at->format('Y/m/d') }}</div>
                        <div style="opacity:.6;">{{ $order->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:5px;align-items:center;">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm" title="تفاصيل">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($order->whatsapp_url)
                            <a href="{{ $order->whatsapp_url }}" target="_blank"
                               class="btn btn-sm" style="background:#25d366;color:#fff;border-color:#25d366;" title="واتساب">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                            <a href="tel:{{ $order->phone }}" class="btn btn-outline btn-sm" title="اتصال">
                                <i class="fas fa-phone"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($orders->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border);">
        <div class="pagination">
            @if ($orders->onFirstPage())
                <span>‹</span>
            @else
                <a href="{{ $orders->previousPageUrl() }}">‹</a>
            @endif
            @foreach ($orders->getUrlRange(max(1, $orders->currentPage()-2), min($orders->lastPage(), $orders->currentPage()+2)) as $page => $url)
                @if ($page == $orders->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if ($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}">›</a>
            @else
                <span>›</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

<script>
(function () {
    const selectAll = document.getElementById('selectAll');
    const bulkBar   = document.getElementById('bulkBar');
    const bulkCount = document.getElementById('bulkCount');
    const hiddenDiv = document.getElementById('bulkHiddenInputs');

    function getChecked() {
        return Array.from(document.querySelectorAll('.order-cb:checked'));
    }

    function updateBar() {
        const checked = getChecked();
        if (checked.length > 0) {
            bulkBar.style.display = 'flex';
            bulkCount.textContent = checked.length;
            // Rebuild hidden inputs
            hiddenDiv.innerHTML = checked
                .map(cb => `<input type="hidden" name="order_ids[]" value="${cb.value}">`)
                .join('');
        } else {
            bulkBar.style.display = 'none';
            hiddenDiv.innerHTML = '';
        }
        // Update row highlight
        document.querySelectorAll('.order-cb').forEach(cb => {
            cb.closest('tr').style.background = cb.checked
                ? 'rgba(212,175,55,.06)' : '';
        });
    }

    // Select-all toggle
    selectAll?.addEventListener('change', () => {
        document.querySelectorAll('.order-cb').forEach(cb => cb.checked = selectAll.checked);
        updateBar();
    });

    // Individual checkboxes
    document.querySelectorAll('.order-cb').forEach(cb => {
        cb.addEventListener('change', () => {
            const all  = document.querySelectorAll('.order-cb');
            const chkd = document.querySelectorAll('.order-cb:checked');
            if (selectAll) selectAll.indeterminate = chkd.length > 0 && chkd.length < all.length;
            if (selectAll) selectAll.checked = chkd.length === all.length;
            updateBar();
        });
    });

    // Click row to toggle checkbox (except links/buttons/selects)
    document.querySelectorAll('.order-row').forEach(row => {
        row.addEventListener('click', e => {
            if (e.target.closest('a,button,select,input,form')) return;
            const cb = row.querySelector('.order-cb');
            if (cb) { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); }
        });
        row.style.cursor = 'pointer';
    });

    window.clearSelection = function () {
        document.querySelectorAll('.order-cb').forEach(cb => cb.checked = false);
        if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
        updateBar();
    };

    window.confirmBulk = function (btn) {
        const form   = document.getElementById('bulkForm');
        const select = form.querySelector('select[name="status"]');
        if (!select.value) { alert('اختر الحالة الجديدة أولاً'); return false; }
        const n = getChecked().length;
        return confirm(`هل تريد تغيير حالة ${n} طلب إلى "${select.options[select.selectedIndex].text}"؟`);
    };
})();
</script>

@endsection
