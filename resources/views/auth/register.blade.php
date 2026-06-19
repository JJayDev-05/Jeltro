<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Jeltro</title>
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
            <h1 class="auth-title">Create an account</h1>
            <p class="auth-subtitle">Join Jeltro and start shopping</p>

            @if($errors->any())
                <div style="background:#fff0f0;border-left:3px solid #c0392b;padding:12px 16px;border-radius:4px;font-size:13px;color:#c0392b;margin-bottom:16px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" autocomplete="name" required
                           placeholder="Your name" value="{{ old('name') }}">
                </div>

                <div class="auth-field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" autocomplete="email" required
                           placeholder="you@example.com" value="{{ old('email') }}">
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" required placeholder="••••••••">
                </div>

                <div class="auth-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           autocomplete="new-password" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn--dark auth-submit">Create account</button>
            </form>

            <p class="auth-switch">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
            </p>
        </div>

    </div>
</main>

</body>
</html>
