@extends('admin.layout')

@section('title', 'رسالة دعم #' . $ticket->id)
@section('page-title', 'رسالة دعم')

@section('topbar-actions')
<a href="{{ route('admin.tickets') }}" class="btn btn-outline btn-sm">
    <i class="fas fa-arrow-right"></i> العودة
</a>
@endsection

@section('content')

@php
$statusLabel = ['new'=>'جديدة','read'=>'مقروءة','replied'=>'تم الرد'];
$badgeStyle  = ['new'=>'background:rgba(231,76,60,.15);color:#e74c3c;','read'=>'background:rgba(243,156,18,.15);color:#f39c12;','replied'=>'background:rgba(39,174,96,.15);color:#2ecc71;'];
@endphp

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

    {{-- Message --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-envelope-open" style="color:var(--gold);margin-left:8px;"></i>
                {{ $ticket->subject ?: 'رسالة بدون موضوع' }}
            </h3>
            <span class="badge" style="{{ $badgeStyle[$ticket->status] ?? '' }}">
                {{ $statusLabel[$ticket->status] ?? $ticket->status }}
            </span>
        </div>
        <div class="card-body">
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:20px;font-size:.95rem;line-height:1.8;white-space:pre-wrap;min-height:120px;">{{ $ticket->message ?: '(لا توجد رسالة)' }}</div>

            @if($ticket->phone)
            <div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap;">
                <a href="https://wa.me/{{ preg_replace('/^0/', '213', $ticket->phone) }}?text={{ urlencode('السلام عليكم ' . ($ticket->name ?: '') . '، نرد على رسالتك...') }}"
                   target="_blank" class="btn btn-primary">
                    <i class="fab fa-whatsapp"></i> رد عبر واتساب
                </a>
                <a href="tel:{{ $ticket->phone }}" class="btn btn-outline">
                    <i class="fas fa-phone"></i> اتصال
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Side panel --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Contact info --}}
        <div class="card">
            <div class="card-header"><h3>معلومات المرسل</h3></div>
            <div class="card-body">
                <div class="detail-row"><span class="detail-label">الاسم</span><span class="detail-value">{{ $ticket->name ?: '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">الهاتف</span><span class="detail-value" style="direction:ltr;">{{ $ticket->phone ?: '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">التاريخ</span><span class="detail-value">{{ $ticket->created_at->format('Y/m/d H:i') }}</span></div>
                <div class="detail-row" style="border:none;"><span class="detail-label">منذ</span><span class="detail-value">{{ $ticket->created_at->diffForHumans() }}</span></div>
            </div>
        </div>

        {{-- Status update --}}
        <div class="card">
            <div class="card-header"><h3>تحديث الحالة</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}">
                    @csrf
                    <select name="status" style="width:100%;margin-bottom:12px;">
                        <option value="new"     {{ $ticket->status === 'new'     ? 'selected' : '' }}>جديدة</option>
                        <option value="read"    {{ $ticket->status === 'read'    ? 'selected' : '' }}>مقروءة</option>
                        <option value="replied" {{ $ticket->status === 'replied' ? 'selected' : '' }}>تم الرد</option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="width:100%;">حفظ</button>
                </form>
            </div>
        </div>

        {{-- Delete --}}
        <form method="POST" action="{{ route('admin.tickets.delete', $ticket) }}"
              onsubmit="return confirm('حذف هذه الرسالة نهائياً؟')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger" style="width:100%;">
                <i class="fas fa-trash"></i> حذف الرسالة
            </button>
        </form>

    </div>
</div>

@endsection
