@extends('layouts.jeltro')

@section('title', 'My Account — Jeltro')

@section('content')

<section class="account-hero">
    <div class="container">
        <span class="section-eyebrow">Account</span>
        <h1 class="account-hero__title">Hi, {{ $user->first_name ?: explode(' ', $user->name)[0] }}.</h1>
        <p class="account-hero__subtitle">Welcome back — manage your profile, orders, and saved pieces.</p>
    </div>
</section>

<div class="account-page">
    <div class="account-layout container">

        <!-- Sidebar -->
        <aside class="account-sidebar">
            <nav class="account-nav">
                <ul>
                    <li class="is-active" id="nav-profile">
                        <a href="#profile"><span>Profile</span></a>
                    </li>
                    <li id="nav-orders">
                        <a href="#orders">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" y1="22" x2="12" y2="12"/></svg>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li id="nav-addresses">
                        <a href="#addresses">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span>Addresses</span>
                        </a>
                    </li>
                    <li id="nav-saved">
                        <a href="#saved">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                            <span>Saved</span>
                        </a>
                    </li>
                    <li class="account-nav__logout">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="#" onclick="this.closest('form').submit(); return false;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                <span>Sign out</span>
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="account-content">
            <div class="account-dashboard">

                <!-- Profile -->
                <section id="profile" class="account-section">
                    <div class="account-section-top">
                        <h2 class="account-section-title">Profile</h2>
                        <span class="account-section-label">Personal details</span>
                    </div>

                    <!-- Avatar -->
                    <div class="account-profile-user">
                        <form method="POST" action="{{ route('account.avatar') }}" enctype="multipart/form-data" id="avatar-form">
                            @csrf
                            <div class="account-avatar {{ $user->avatar ? 'account-avatar--photo' : '' }}"
                                 id="account-avatar"
                                 @if($user->avatar) style="background-image:url('{{ asset('storage/' . $user->avatar) }}')" @endif>
                                @if(!$user->avatar)
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none"
                                   onchange="document.getElementById('avatar-form').submit()">
                        </form>
                        <div>
                            <p class="account-profile-name">{{ $user->name }}</p>
                            <p class="account-profile-email">{{ $user->email }}</p>
                            <button type="button" class="account-change-photo"
                                    onclick="document.getElementById('avatar-input').click()">
                                Change photo
                            </button>
                        </div>
                    </div>

                    <!-- Profile form -->
                    <form class="account-profile-form" method="POST" action="{{ route('account.update') }}">
                        @csrf @method('PATCH')
                        <div class="account-form-grid">
                            <div class="account-form-field">
                                <label for="first_name">First name</label>
                                <input type="text" id="first_name" name="first_name" required
                                       value="{{ old('first_name', $user->first_name ?: explode(' ', $user->name)[0]) }}">
                                @error('first_name') <span style="color:#c0392b;font-size:12px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="account-form-field">
                                <label for="last_name">Last name</label>
                                <input type="text" id="last_name" name="last_name"
                                       value="{{ old('last_name', $user->last_name ?? '') }}">
                                @error('last_name') <span style="color:#c0392b;font-size:12px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="account-form-field account-form-field--full">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required
                                       value="{{ old('email', $user->email) }}">
                                @error('email') <span style="color:#c0392b;font-size:12px;">{{ $message }}</span> @enderror
                            </div>
                            <div class="account-form-field account-form-field--full">
                                <label for="gender">Gender</label>
                                <select id="gender" name="gender"
                                        style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;color:inherit;">
                                    <option value="">Prefer not to say</option>
                                    <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="prefer_not_to_say" {{ old('gender', $user->gender) === 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                            </div>
                        </div>
                        @if($errors->any())
                            <p style="font-size:13px;color:#c0392b;font-weight:500;margin-bottom:12px;">Please fix the errors above and try again.</p>
                        @endif
                        <div class="account-form-actions">
                            <button type="submit" class="btn btn--dark">Save changes</button>
                            <button type="reset" class="btn btn--ghost">Cancel</button>
                        </div>
                    </form>
                </section>

                <hr class="account-separator">

                <!-- Orders -->
                <section id="orders" class="account-section">
                    <div class="account-section-top">
                        <h2 class="account-section-title">Orders</h2>
                        <a href="{{ route('shop') }}" class="account-section-link">Continue shopping</a>
                    </div>
                    @if($orders->isEmpty())
                        <div class="account-orders-empty">
                            <p>No orders yet.</p>
                            <a href="{{ route('shop') }}" class="btn btn--dark" style="margin-top:8px;">Shop Now</a>
                        </div>
                    @else
                        <table style="width:100%;border-collapse:collapse;font-size:14px;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--border);text-align:left;">
                                    <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;">Order</th>
                                    <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;">Date</th>
                                    <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;">Status</th>
                                    <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;">Items</th>
                                    <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;text-align:right;">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr style="border-bottom:1px solid var(--border);">
                                        <td style="padding:14px 0;font-weight:600;">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td style="padding:14px 0;color:#666;">{{ $order->created_at->format('M d, Y') }}</td>
                                        <td style="padding:14px 0;">
                                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:#f0f0ec;text-transform:capitalize;">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                        </td>
                                        <td style="padding:14px 0;color:#666;">
                                            {{ collect($order->items)->sum('quantity') }} item(s)
                                        </td>
                                        <td style="padding:14px 0;font-weight:600;text-align:right;">${{ number_format($order->total, 2) }}</td>
                                        <td style="padding:14px 0;text-align:right;">
                                            <a href="{{ route('account.order', $order) }}" style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </section>

                <hr class="account-separator">

                <!-- Addresses -->
                <section id="addresses" class="account-section">
                    <div class="account-section-top">
                        <h2 class="account-section-title">Addresses</h2>
                        <button type="button" class="account-section-link" onclick="openAddressForm()">Add new</button>
                    </div>

                    <!-- Address form (hidden by default) -->
                    <div id="address-form-wrap" style="display:none; margin-bottom:32px;">
                        <p id="address-form-title" style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">New address</p>
                        <form method="POST" action="{{ route('address.store') }}" id="address-form" onsubmit="preparePhone()">
                            @csrf
                            <input type="hidden" name="type" value="billing" id="address-type">
                            <input type="hidden" name="phone" id="phone-hidden">
                            <input type="hidden" name="address_id" id="address-id">
                            <div class="address-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                                <div class="account-form-field" style="grid-column:span 2;">
                                    <label>Full name <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="name" id="addr-name" required placeholder="Your full name"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field" style="grid-column:span 2;">
                                    <label>Phone number</label>
                                    <input type="tel" id="phone-input" placeholder="+63 917 123 4567"
                                           pattern="[0-9\s\+]{10,16}" maxlength="16"
                                           oninput="this.value=this.value.replace(/[^0-9\s\+]/g,'')"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field" style="grid-column:span 2;">
                                    <label>Street address <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="address_1" id="addr-address1" required placeholder="House number and street name"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field">
                                    <label>Region <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="region" id="addr-region" required placeholder="e.g. NCR"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field">
                                    <label>Province <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="province" id="addr-province" required placeholder="e.g. Metro Manila"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field">
                                    <label>City / Municipality <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="city" id="addr-city" required placeholder="e.g. Quezon City"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field">
                                    <label>Barangay <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="barangay" id="addr-barangay" required placeholder="e.g. Brgy. Holy Spirit"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                                <div class="account-form-field">
                                    <label>Postcode <span style="color:var(--accent);">*</span></label>
                                    <input type="text" name="postcode" id="addr-postcode" required placeholder="e.g. 1127"
                                           style="width:100%;height:48px;padding:0 14px;border:1px solid var(--border);border-radius:6px;font-family:var(--font-body);font-size:14px;">
                                </div>
                            </div>
                            <div class="account-form-actions">
                                <button type="submit" class="btn btn--dark">Save address</button>
                                <button type="button" class="btn btn--ghost" onclick="closeAddressForm()">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <div class="account-address-grid">
                        @forelse($addresses as $address)
                            <div class="account-address-card">
                                <div class="account-address-card__top">
                                    <span class="account-address-card__badge">{{ $address->is_default ? 'Default' : ucfirst($address->type) }}</span>
                                    <button type="button" style="all:unset;cursor:pointer;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text);"
                                            data-id="{{ $address->id }}"
                                            data-type="{{ $address->type }}"
                                            data-name="{{ $address->name }}"
                                            data-phone="{{ $address->phone }}"
                                            data-address1="{{ $address->address_1 }}"
                                            data-region="{{ $address->region }}"
                                            data-province="{{ $address->province }}"
                                            data-city="{{ $address->city }}"
                                            data-barangay="{{ $address->barangay }}"
                                            data-postcode="{{ $address->postcode }}"
                                            onclick="editAddress(this.dataset)">Edit</button>
                                </div>
                                <p class="account-address-card__name">{{ $address->name }}</p>
                                @if($address->phone)
                                    <p class="account-address-card__line">+63 {{ $address->phone }}</p>
                                @endif
                                <p class="account-address-card__line">{{ $address->address_1 }}</p>
                                @php
                                    $cityLine = collect([$address->barangay, $address->city])->filter()->implode(', ');
                                    $regionLine = collect([$address->province, $address->region])->filter()->implode(', ');
                                    if ($address->postcode) $regionLine .= ' ' . $address->postcode;
                                @endphp
                                @if($cityLine)<p class="account-address-card__line">{{ $cityLine }}</p>@endif
                                @if($regionLine)<p class="account-address-card__line">{{ $regionLine }}</p>@endif
                                @if(!$address->is_default)
                                    <div style="display:flex;gap:16px;margin-top:16px;border-top:1px solid var(--border);padding-top:12px;">
                                        <form method="POST" action="{{ route('address.default', $address) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" style="all:unset;cursor:pointer;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);">Set as default</button>
                                        </form>
                                        <form method="POST" action="{{ route('address.destroy', $address) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="all:unset;cursor:pointer;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text);"
                                                    onclick="return confirm('Remove this address?')">Remove</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="account-address-card">
                                <div class="account-address-card__top">
                                    <span class="account-address-card__badge">Default</span>
                                </div>
                                <p class="account-address-card__empty">No billing address saved yet.</p>
                            </div>
                        @endforelse

                        <button type="button" class="account-address-add" onclick="openAddressForm()">
                            + Add another address
                        </button>
                    </div>
                </section>

                <hr class="account-separator">

                <!-- Saved -->
                <section id="saved" class="account-section">
                    <div class="account-section-top">
                        <h2 class="account-section-title">Saved</h2>
                        <a href="{{ route('shop') }}" class="account-section-link">Browse shop</a>
                    </div>
                    @if($saved->isEmpty())
                        <div class="account-orders-empty">
                            <p>No saved pieces yet.</p>
                            <a href="{{ route('shop') }}" class="btn btn--dark" style="margin-top:8px;">Browse Shop</a>
                        </div>
                    @else
                        <div class="account-saved-grid">
                            @foreach($saved as $product)
                                <div class="product-card">
                                    <a href="{{ route('shop.show', $product->slug) }}" class="product-card__link">
                                        <div class="product-card__image">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" loading="lazy">
                                            @else
                                                <div class="product-card__placeholder"></div>
                                            @endif
                                        </div>
                                        <div class="product-card__info">
                                            <span class="product-card__name">{{ $product->name }}</span>
                                            <span class="product-card__price">${{ number_format($product->price, 2) }}</span>
                                        </div>
                                    </a>
                                    <button class="product-save-btn is-saved"
                                            data-url="{{ route('saved.toggle', $product) }}"
                                            onclick="toggleSaveAccount(this)"
                                            title="Remove from saved">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

            </div>
        </div>

    </div>
</div>

<script>
document.querySelectorAll('.account-nav a').forEach(link => {
    link.addEventListener('click', () => {
        document.querySelectorAll('.account-nav li').forEach(li => li.classList.remove('is-active'));
        link.closest('li').classList.add('is-active');
    });
});

function openAddressForm() {
    document.getElementById('address-form-title').textContent = 'New address';
    document.getElementById('address-form').reset();
    document.getElementById('phone-input').value = '';
    document.getElementById('address-type').value = 'billing';
    document.getElementById('address-id').value = '';
    const wrap = document.getElementById('address-form-wrap');
    wrap.style.display = 'block';
    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function closeAddressForm() {
    document.getElementById('address-form-wrap').style.display = 'none';
}

function editAddress(data) {
    document.getElementById('address-form-title').textContent = 'Edit address';
    document.getElementById('address-id').value     = data.id       || '';
    document.getElementById('address-type').value   = data.type     || '';
    document.getElementById('addr-name').value      = data.name     || '';
    document.getElementById('addr-address1').value  = data.address1 || '';
    document.getElementById('addr-region').value    = data.region   || '';
    document.getElementById('addr-province').value  = data.province || '';
    document.getElementById('addr-city').value      = data.city     || '';
    document.getElementById('addr-barangay').value  = data.barangay || '';
    document.getElementById('addr-postcode').value  = data.postcode || '';
    document.getElementById('phone-input').value = data.phone || '';
    const wrap = document.getElementById('address-form-wrap');
    wrap.style.display = 'block';
    wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function preparePhone() {
    document.getElementById('phone-hidden').value = document.getElementById('phone-input').value.trim();
}

function toggleSaveAccount(btn) {
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.saved) {
            btn.closest('.product-card').remove();
        }
    });
}
</script>

@endsection
