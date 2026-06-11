<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Y5Home CRM</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}?v={{ time() }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #034b25 0%, #022512 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
        .brand { text-align: center; margin-bottom: .5rem; }
        .subtitle { text-align: center; color: #888; font-size: .85rem; margin-bottom: 2rem; }
        .btn-login { background: #034b25; color: #fff; width: 100%; padding: .75rem; font-weight: 600; border: none; border-radius: 8px; }
        .btn-login:hover, .btn-login:focus, .btn-login:active { background: #02381c; color: #fff; outline: none; }
        .form-control:focus { border-color: #034b25; box-shadow: 0 0 0 .15rem rgba(3,75,37,.15); }
    </style>
</head>
<body>
<div class="login-card">
    <div class="brand">
        <img src="{{ asset('Y5home_Technologies.webp') }}" alt="Y5Home Logo" style="max-height: 50px; width: auto; margin-bottom: 0.5rem;">
    </div>
    <div class="subtitle">CRM & Experience Center Management</div>

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-500">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="you@y5home.com" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-500">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                   placeholder="••••••••" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div class="form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small" for="remember">Remember me</label>
            </div>
        </div>
        <button type="submit" class="btn btn-login">Sign In</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
