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
        .login-box { background: #1a1a10; border: 1px solid rgba(212,175,55,.25); border-radius: 16px; padding: 48px 40px; width: 100%; max-width: 380px; }
        .login-logo { text-align: center; margin-bottom: 32px; }
        .login-logo h1 { font-size: 1.4rem; color: #d4af37; font-weight: 800; }
        .login-logo p { font-size: .85rem; color: #888; margin-top: 6px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: .85rem; color: #888; margin-bottom: 6px; font-weight: 600; }
        input[type=password] { width: 100%; background: #0f0f0a; border: 1px solid rgba(212,175,55,.25); border-radius: 8px; color: #e8e8d8; font-family: inherit; font-size: .95rem; padding: 11px 16px; outline: none; transition: border-color .2s; direction: ltr; }
        input[type=password]:focus { border-color: #d4af37; }
        .btn { width: 100%; padding: 13px; background: #d4af37; color: #111; font-family: inherit; font-size: 1rem; font-weight: 800; border: none; border-radius: 8px; cursor: pointer; transition: opacity .15s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn:hover { opacity: .85; }
        .error { background: rgba(231,76,60,.1); border: 1px solid rgba(231,76,60,.3); border-radius: 8px; padding: 10px 14px; color: #e74c3c; font-size: .85rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">
        <h1><i class="fas fa-store"></i> لوحة التحكم</h1>
        <p>ورشة يحيى للموس البوسعادي</p>
    </div>

    @if ($errors->any())
    <div class="error"><i class="fas fa-times-circle"></i> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" id="password" name="password" placeholder="••••••••" autofocus>
        </div>
        <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> دخول</button>
    </form>
</div>
</body>
</html>
