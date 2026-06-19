@extends('layouts.jeltro')

@section('title', 'Shop — Jeltro')

@section('content')

<div class="shop-page">
    <div class="shop-header container">
        <span class="section-eyebrow">All Pieces</span>
        <h1 class="shop-header__title">The Collection</h1>
        <p class="shop-header__count">{{ $products->count() }} {{ Str::plural('piece', $products->count()) }}</p>
    </div>

    <div class="container">
        @if($products->count())
            <div class="shop-grid">
                @foreach($products as $product)
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
                        @auth
                            <button class="product-save-btn {{ in_array($product->id, $savedIds) ? 'is-saved' : '' }}"
                                    data-url="{{ route('saved.toggle', $product) }}"
                                    onclick="toggleSave(this)"
                                    title="{{ in_array($product->id, $savedIds) ? 'Remove from saved' : 'Save' }}">
                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                                </svg>
                            </button>
                        @endauth
                    </div>
                @endforeach
            </div>
        @else
            <div class="shop-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
                <p class="shop-empty__title">No products found</p>
                <p class="shop-empty__desc">Check back soon — new pieces are on their way.</p>
            </div>
        @endif
    </div>
</div>

@auth
<script>
function toggleSave(btn) {
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.classList.toggle('is-saved', data.saved);
        btn.title = data.saved ? 'Remove from saved' : 'Save';
    });
}
</script>
@endauth

@endsection
