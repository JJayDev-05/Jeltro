<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Jeltro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">
    <style>
        .admin-wrap { display:flex; min-height:100vh; }
        .admin-sidebar { width:220px; background:#1c1a14; padding:32px 0; flex-shrink:0; }
        .admin-sidebar__logo { display:flex !important; justify-content:center; font-family:var(--font-heading); font-size:52px; font-weight:400; color:#fff; text-align:center; margin-bottom:36px; text-decoration:none; letter-spacing:.04em; padding:0 !important; }
        .admin-sidebar__label { font-size:10px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:rgba(255,255,255,.35); padding:0 24px; margin-bottom:6px; }
        .admin-sidebar a { display:flex; align-items:center; padding:10px 24px; font-size:13px; font-weight:500; color:rgba(255,255,255,.75); text-decoration:none; transition:color .15s,background .15s; }
        .admin-sidebar a:hover { color:#fff; background:rgba(255,255,255,.07); }
        .admin-sidebar a.active { color:#fff; background:rgba(255,255,255,.1); border-left:2px solid var(--accent); font-weight:600; }
        .admin-main { flex:1; background:var(--bg); min-height:100vh; }
        .admin-topbar { background:var(--bg-alt); border-bottom:1px solid var(--border); padding:0 32px; height:56px; display:flex; align-items:center; justify-content:space-between; }
        .admin-topbar__title { font-size:13px; font-weight:600; letter-spacing:.06em; text-transform:uppercase; color:var(--text-muted); }
        .admin-topbar__user { font-size:13px; color:var(--text-muted); display:flex; align-items:center; gap:16px; }
        .admin-topbar__user a { color:var(--text-muted); font-size:13px; text-decoration:none; }
        .admin-topbar__user a:hover { color:var(--text); }
        .admin-content { padding:32px; }
        .admin-card { background:oklch(0.99 0.008 85); border:1px solid var(--border); border-radius:8px; padding:28px 32px; }
        .admin-table { width:100%; border-collapse:collapse; font-size:14px; }
        .admin-table th { text-align:left; padding:10px 12px; font-size:11px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--text-muted); border-bottom:2px solid var(--border); }
        .admin-table td { padding:14px 12px; border-bottom:1px solid var(--border); vertical-align:middle; }
        .admin-table tr:last-child td { border-bottom:none; }
        .admin-table tr:hover td { background:var(--bg-alt); }
        .admin-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:4px; font-size:12px; font-weight:600; letter-spacing:.06em; text-decoration:none; border:none; cursor:pointer; transition:opacity .15s; font-family:var(--font-body); }
        .admin-btn:hover { opacity:.85; }
        .admin-btn--dark { background:var(--bg-dark); color:#f5f0e8; }
        .admin-btn--outline { background:transparent; color:var(--text); border:1px solid var(--border); }
        .admin-btn--danger { background:#b91c1c; color:#fff; }
        .admin-btn--sm { padding:5px 12px; font-size:11px; }
        .form-group { margin-bottom:20px; }
        .form-label { display:block; font-size:11px; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--text-muted); margin-bottom:6px; }
        .form-input { width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:4px; font-size:14px; background:oklch(0.99 0.008 85); font-family:var(--font-body); box-sizing:border-box; color:var(--text); }
        .form-input:focus { outline:none; border-color:var(--text); }
        .form-hint { font-size:12px; color:var(--text-muted); margin-top:4px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .form-error { font-size:12px; color:#b91c1c; margin-top:4px; }
        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge--gray { background:var(--bg-alt); color:var(--text-muted); }
        .alert { padding:12px 16px; border-radius:4px; font-size:13px; margin-bottom:20px; }
        .alert--success { background:#f0fff4; border-left:3px solid #27ae60; color:#1a5c35; }
    </style>
</head>
<body style="margin:0;font-family:var(--font-body,Inter,sans-serif);">

<div class="admin-wrap">
    <aside class="admin-sidebar">
        <a href="{{ route('home') }}" class="admin-sidebar__logo" style="font-size:24px;">Jeltro</a>
        <p class="admin-sidebar__label">Catalog</p>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.index') || request()->routeIs('admin.products.create') || request()->routeIs('admin.products.edit') ? 'active' : '' }}">Products</a>
        <a href="{{ route('admin.products.archived') }}" class="{{ request()->routeIs('admin.products.archived') ? 'active' : '' }}">Archived</a>
        <p class="admin-sidebar__label" style="margin-top:24px;">Orders</p>
        <a href="{{ route('admin.orders.index', ['tab' => 'pending']) }}" class="{{ request()->routeIs('admin.orders.*') && request()->query('tab', 'pending') === 'pending' ? 'active' : '' }}">
            Pending
            @php $pendingCount = cache()->remember('admin_pending_count', 60, fn() => \App\Models\Order::where('status','pending')->count()); $cancelCount = cache()->remember('admin_cancel_count', 60, fn() => \App\Models\Order::where('cancel_requested', true)->count()); @endphp
            @if($pendingCount > 0)
                <span style="margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">{{ $pendingCount }}</span>
            @endif
            @if($cancelCount > 0)
                <span style="margin-left:4px;background:#b91c1c;color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;" title="Cancel requests">{{ $cancelCount }} cancel</span>
            @endif
        </a>
        <a href="{{ route('admin.orders.index', ['tab' => 'on_delivery']) }}" class="{{ request()->routeIs('admin.orders.*') && request()->query('tab') === 'on_delivery' ? 'active' : '' }}">On Delivery</a>
        <a href="{{ route('admin.orders.index', ['tab' => 'completed']) }}" class="{{ request()->routeIs('admin.orders.*') && request()->query('tab') === 'completed' ? 'active' : '' }}">Completed</a>
        <a href="{{ route('admin.orders.index', ['tab' => 'cancelled']) }}" class="{{ request()->routeIs('admin.orders.*') && request()->query('tab') === 'cancelled' ? 'active' : '' }}">Cancelled</a>
        <p class="admin-sidebar__label" style="margin-top:24px;">Store</p>
        <a href="{{ route('home') }}" target="_blank">View Site &nearr;</a>
    </aside>

    <div class="admin-main">
        <div class="admin-topbar">
            <span class="admin-topbar__title">@yield('title', 'Dashboard')</span>
            <div class="admin-topbar__user">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" style="all:unset;cursor:pointer;font-size:13px;color:#666;">Log out</button>
                </form>
            </div>
        </div>

        <div class="admin-content">
            @if(session('success'))
                <div class="alert alert--success">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</div>

<script>
document.addEventListener('submit', function(e) {
    const btn = e.target.querySelector('[type="submit"]');
    if (btn && !btn.dataset.noDisable) {
        setTimeout(() => { btn.disabled = true; btn.style.opacity = '0.6'; }, 0);
    }
});
</script>
</body>
</html>
