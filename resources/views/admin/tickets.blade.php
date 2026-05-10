@extends('admin.layout')

@section('title', 'رسائل الدعم')
@section('page-title', 'رسائل الدعم')

@section('content')

@php
$statusLabel = ['new'=>'جديدة','read'=>'مقروءة','replied'=>'تم الرد'];
$badgeStyle  = ['new'=>'background:rgba(231,76,60,.15);color:#e74c3c;','read'=>'background:rgba(243,156,18,.15);color:#f39c12;','replied'=>'background:rgba(39,174,96,.15);color:#2ecc71;'];
@endphp

{{-- Filter tabs --}}
<div class="card-header" style="margin-bottom:20px;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;">
    <div class="filter-tabs">
        <a href="{{ route('admin.tickets') }}"
           class="filter-tab {{ !$status || $status === 'all' ? 'active' : '' }}">
            الكل <span style="opacity:.6;">({{ $counts['all'] }})</span>
        </a>
        <a href="{{ route('admin.tickets', ['status'=>'new']) }}"
           class="filter-tab {{ $status === 'new' ? 'active' : '' }}"
           style="{{ $counts['new'] > 0 ? 'border-color:#e74c3c;color:#e74c3c;' : '' }}">
            جديدة <span style="opacity:.6;">({{ $counts['new'] }})</span>
        </a>
        <a href="{{ route('admin.tickets', ['status'=>'read']) }}"
           class="filter-tab {{ $status === 'read' ? 'active' : '' }}">
            مقروءة <span style="opacity:.6;">({{ $counts['read'] }})</span>
        </a>
        <a href="{{ route('admin.tickets', ['status'=>'replied']) }}"
           class="filter-tab {{ $status === 'replied' ? 'active' : '' }}">
            تم الرد <span style="opacity:.6;">({{ $counts['replied'] }})</span>
        </a>
    </div>
</div>

<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>الموضوع</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr style="{{ $ticket->status === 'new' ? 'background:rgba(231,76,60,.04);' : '' }}">
                    <td class="text-muted" style="font-size:.8rem;">{{ $ticket->id }}</td>
                    <td style="font-weight:{{ $ticket->status === 'new' ? '700' : '400' }};">
                        {{ $ticket->name ?: '—' }}
                    </td>
                    <td style="direction:ltr;font-size:.88rem;">{{ $ticket->phone ?: '—' }}</td>
                    <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        {{ $ticket->subject ?: substr($ticket->message, 0, 60) }}
                    </td>
                    <td>
                        <span class="badge" style="{{ $badgeStyle[$ticket->status] ?? '' }}">
                            {{ $statusLabel[$ticket->status] ?? $ticket->status }}
                        </span>
                    </td>
                    <td class="text-muted" style="font-size:.8rem;white-space:nowrap;">
                        {{ $ticket->created_at->diffForHumans() }}
                    </td>
                    <td style="display:flex;gap:6px;align-items:center;">
                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-outline btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.tickets.delete', $ticket) }}"
                              onsubmit="return confirm('حذف هذه الرسالة؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--text-muted);">
                        <i class="fas fa-inbox" style="font-size:2rem;opacity:.3;display:block;margin-bottom:10px;"></i>
                        لا توجد رسائل
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tickets->hasPages())
    <div style="padding:16px 20px;border-top:1px solid var(--border);">
        <div class="pagination">
            @if($tickets->onFirstPage())
                <span>‹</span>
            @else
                <a href="{{ $tickets->previousPageUrl() }}">‹</a>
            @endif

            @foreach($tickets->getUrlRange(1, $tickets->lastPage()) as $page => $url)
                @if($page == $tickets->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($tickets->hasMorePages())
                <a href="{{ $tickets->nextPageUrl() }}">›</a>
            @else
                <span>›</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection
