<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Jeltro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>

<main id="main-content" class="auth-page">
    <div class="auth-wrap">

        <div class="auth-brand">
            <a href="{{ route('home') }}" class="auth-logo">Jeltro</a>
        </div>

        <div class="auth-box">
            <h1 class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to continue shopping</p>

            @if(session('status'))
                <div style="background:#f0fff4;border-left:3px solid #27ae60;padding:12px 16px;border-radius:4px;font-size:13px;color:#27ae60;margin-bottom:16px;">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#fff0f0;border-left:3px solid #c0392b;padding:12px 16px;border-radius:4px;font-size:13px;color:#c0392b;margin-bottom:16px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                           placeholder="you@example.com" value="{{ old('email') }}">
                </div>

                <div class="auth-field">
                    <label for="password">
                        Password
                        <a href="{{ route('password.request') }}" class="auth-forgot">Forgot password?</a>
                    </label>
                    <input type="password" id="password" name="password"
                           autocomplete="current-password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn--dark auth-submit">Sign in</button>
            </form>

            <p class="auth-switch">
                Don't have an account?
                <a href="{{ route('register') }}">Create one</a>
            </p>
        </div>

    </div>
</main>

</body>
</html>
