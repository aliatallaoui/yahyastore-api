@extends('admin.layout')

@section('title', 'الملف الشخصي')
@section('page-title', 'الملف الشخصي')

@section('content')
<div style="max-width: 520px; display: flex; flex-direction: column; gap: 24px;">

    {{-- Change Name --}}
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-signature" style="color:var(--gold);margin-left:8px;"></i> الاسم</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.profile.name') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label for="name">الاسم الظاهر</label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                           style="width:100%;" required maxlength="100">
                    @error('name')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ الاسم</button>
            </form>
        </div>
    </div>

    {{-- Change Password --}}
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-lock" style="color:var(--gold);margin-left:8px;"></i> تغيير كلمة المرور</h3></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.profile.password') }}">
                @csrf
                <div style="margin-bottom:14px;">
                    <label for="current_password">كلمة المرور الحالية</label>
                    <input type="password" id="current_password" name="current_password"
                           style="width:100%;" required autocomplete="current-password">
                    @error('current_password')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:14px;">
                    <label for="new_password">كلمة المرور الجديدة</label>
                    <input type="password" id="new_password" name="new_password"
                           style="width:100%;" required autocomplete="new-password" minlength="8">
                    @error('new_password')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div style="margin-bottom:20px;">
                    <label for="new_password_confirmation">تأكيد كلمة المرور الجديدة</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                           style="width:100%;" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-key"></i> تغيير كلمة المرور</button>
            </form>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-info-circle" style="color:var(--gold);margin-left:8px;"></i> معلومات الحساب</h3></div>
        <div class="card-body">
            <div class="detail-row">
                <span class="detail-label">البريد الإلكتروني</span>
                <span class="detail-value" style="direction:ltr;">{{ Auth::user()->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">الصلاحية</span>
                <span class="detail-value" style="color:var(--gold);">مشرف</span>
            </div>
        </div>
    </div>

</div>
@endsection
