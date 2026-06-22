<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jeltro')</title>
    <link rel="icon" type="image/x-icon" href="/images/jeltro-icon.ico">
    <link rel="shortcut icon" href="/images/jeltro-icon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>

<header id="site-header">
    <div class="header-inner">
        <nav class="header-nav">
            <a href="{{ route('shop') }}">Shop</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('journal') }}">Journal</a>
        </nav>

        <a href="{{ route('home') }}" class="site-logo">Jeltro</a>

        <div class="header-actions">
            <button id="search-btn" type="button" class="header-icon-btn" data-tooltip="Search">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </button>

            @auth
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                <div class="notif-wrap" id="notif-wrap">
                    <button onclick="toggleNotif()" class="header-icon-btn" data-tooltip="Notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        @if($unreadCount > 0)
                            <span class="notif-badge">{{ $unreadCount }}</span>
                        @endif
                    </button>

                    <div class="notif-dropdown" id="notif-dropdown">
                        <div class="notif-dropdown__header">
                            <span class="notif-dropdown__title">Notifications</span>
                            @if($unreadCount > 0)
                                <a href="{{ route('notifications.read') }}" class="notif-dropdown__mark-read">Mark all read</a>
                            @endif
                        </div>
                        <div class="notif-dropdown__list">
                            @php $notifications = auth()->user()->notifications()->latest()->take(20)->get(); @endphp
                            @forelse($notifications as $notif)
                                <div class="notif-item {{ $notif->read_at ? '' : 'is-unread' }}">
                                    <a href="{{ route('account.order', $notif->data['order_id']) }}" class="notif-item__link">
                                        <div class="notif-item__inner">
                                            <span class="notif-dot {{ $notif->read_at ? '' : 'is-unread' }}"></span>
                                            <div>
                                                <p class="notif-item__order">Order #{{ $notif->data['order_no'] }}</p>
                                                <p class="notif-item__msg">{{ $notif->data['message'] }}</p>
                                                <p class="notif-item__time">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <button type="button"
                                            data-notif-id="{{ $notif->id }}"
                                            onclick="deleteNotif(this)"
                                            class="notif-item__delete"
                                            title="Remove">&times;</button>
                                </div>
                            @empty
                                <p class="notif-empty">No notifications yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.products.index') }}" class="header-icon-btn" data-tooltip="Admin Panel">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('account') }}" class="header-icon-btn" data-tooltip="Account">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="header-btn header-btn--login">Log in</a>
                <a href="{{ route('register') }}" class="header-btn header-btn--register">Sign up</a>
            @endauth

            @auth
                <a href="{{ route('cart.index') }}" class="header-icon-btn" data-tooltip="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                    </svg>
                    @php $cartCount = array_sum(array_column(session()->get('cart', []), 'quantity')); @endphp
                    @if($cartCount > 0)
                        <span class="cart-count-badge">{{ $cartCount }}</span>
                    @endif
                </a>
            @endauth
        </div>
    </div>
</header>

<main id="main-content">
    @yield('content')
</main>

@if(session('success'))
<div class="toast" id="site-toast" role="alert">
    <span class="toast__icon">
        <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <polyline points="2,6 5,9 10,3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
    <span class="toast__msg">{{ session('success') }}</span>
    <button class="toast__close" onclick="dismissToast()" aria-label="Close">&times;</button>
</div>
@endif

<footer id="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="site-logo">Jeltro</a>
            <p class="footer-tagline">Minimal clothing for the modern wardrobe. Quality over quantity, always.</p>
        </div>
        <div class="footer-nav">
            <h4>Shop</h4>
            <ul>
                <li><a href="{{ route('shop') }}">All Products</a></li>
                <li><a href="{{ route('cart.index') }}">Cart</a></li>
            </ul>
        </div>
        <div class="footer-nav">
            <h4>Account</h4>
            <ul>
                @auth
                    <li><a href="{{ route('account') }}">My Account</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="footer-logout-btn">Log out</button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; {{ date('Y') }} Jeltro. All rights reserved.</span>
        <span>Jeltro</span>
    </div>
</footer>

<script>
document.addEventListener('submit', function(e) {
    const btn = e.target.querySelector('[type="submit"]');
    if (btn && !btn.dataset.noDisable) {
        setTimeout(() => { btn.disabled = true; btn.style.opacity = '0.6'; }, 0);
    }
});
document.getElementById('search-btn').addEventListener('click', function() {
    window.location.href = '{{ route('search') }}';
});
function toggleNotif() {
    const d = document.getElementById('notif-dropdown');
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('notif-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notif-dropdown').style.display = 'none';
    }
});
var _toastTimer;
function dismissToast() {
    var t = document.getElementById('site-toast');
    if (!t) return;
    t.classList.remove('is-visible');
    clearTimeout(_toastTimer);
}
(function() {
    var t = document.getElementById('site-toast');
    if (!t) return;
    requestAnimationFrame(function() { t.classList.add('is-visible'); });
    _toastTimer = setTimeout(dismissToast, 4000);
})();
function deleteNotif(btn) {
    const id = btn.dataset.notifId;
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                         || '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    }).then(() => {
        btn.closest('.notif-item').remove();
    });
}
</script>

@include('partials.chatbot')
</body>
</html>
