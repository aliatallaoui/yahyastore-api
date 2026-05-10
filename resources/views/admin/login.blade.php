<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دخول لوحة التحكم | ورشة يحيى</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Cairo', sans-serif; background: #0f0f0a; color: #e8e8d8; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-wrap { width: 100%; max-width: 400px; padding: 20px; }
        .login-box { background: #1a1a10; border: 1px solid rgba(212,175,55,.25); border-radius: 16px; padding: 48px 40px; }
        .login-logo { text-align: center; margin-bottom: 32px; }
        .login-logo .icon { width: 60px; height: 60px; background: rgba(212,175,55,.1); border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.6rem; color: #d4af37; }
        .login-logo h1 { font-size: 1.4rem; color: #d4af37; font-weight: 800; }
        .login-logo p { font-size: .82rem; color: #666; margin-top: 6px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: .82rem; color: #888; margin-bottom: 7px; font-weight: 700; }
        .input-wrap { position: relative; }
        .input-wrap i { position: absolute; top: 50%; transform: translateY(-50%); right: 14px; color: #555; font-size: .85rem; pointer-events: none; }
        input[type=email], input[type=password] {
            width: 100%; background: #0f0f0a; border: 1px solid rgba(212,175,55,.2);
            border-radius: 9px; color: #e8e8d8; font-family: inherit; font-size: .95rem;
            padding: 11px 40px 11px 16px; outline: none; transition: border-color .2s;
            direction: ltr; text-align: left;
        }
        input[type=email]:focus, input[type=password]:focus { border-color: #d4af37; }
        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; cursor: pointer; }
        .remember-row input[type=checkbox] { width: 16px; height: 16px; accent-color: #d4af37; cursor: pointer; flex-shrink: 0; }
        .remember-row span { font-size: .83rem; color: #888; }
        .btn-login { width: 100%; padding: 13px; background: #d4af37; color: #111; font-family: inherit; font-size: 1rem; font-weight: 800; border: none; border-radius: 9px; cursor: pointer; transition: opacity .15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-login:hover { opacity: .85; }
        .error { background: rgba(231,76,60,.08); border: 1px solid rgba(231,76,60,.3); border-radius: 9px; padding: 11px 16px; color: #e74c3c; font-size: .85rem; margin-bottom: 20px; display: flex; align-items: center; gap: 9px; }
        .lock-notice { text-align: center; margin-top: 20px; font-size: .78rem; color: #555; display: flex; align-items: center; justify-content: center; gap: 6px; }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <div class="login-logo">
            <div class="icon"><i class="fas fa-store"></i></div>
            <h1>لوحة التحكم</h1>
            <p>ورشة يحيى للموس البوسعادي</p>
        </div>

        @if ($errors->any())
        <div class="error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           placeholder="admin@example.com" autofocus autocomplete="email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password"
                           placeholder="••••••••" autocomplete="current-password">
                </div>
            </div>

            <label class="remember-row">
                <input type="checkbox" name="remember" value="1">
                <span>تذكرني لمدة 30 يوماً</span>
            </label>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> دخول
            </button>
        </form>

        <p class="lock-notice"><i class="fas fa-shield-alt"></i> يُقفل الحساب بعد 5 محاولات فاشلة</p>
    </div>
</div>
</body>
</html>
