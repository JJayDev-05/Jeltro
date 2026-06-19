@extends('layouts.jeltro')

@section('title', 'Jeltro — Custom Apparel Studio')

@section('content')

<section class="hero">
    <div class="hero__content">
        <span class="section-eyebrow">Custom Apparel Studio</span>
        <h1 class="hero__title">Wear something <em>only</em> yours.</h1>
        <p class="hero__desc">Jeltro is a custom apparel studio. Choose a blank, add your art, type, or photo we print, stitch, and ship each piece to order. No minimums, no leftovers.</p>
        <div class="hero__actions">
            <a href="{{ route('shop') }}" class="btn btn--dark">Start Designing &rarr;</a>
            <a href="#how-it-works" class="btn btn--link">How It Works</a>
        </div>
    </div>
    <div class="hero__image">
        <img src="{{ asset('images/hero.jpg') }}" alt="Jeltro Custom Apparel" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
    </div>
</section>

<div class="ticker-bar" aria-hidden="true">
    <div class="ticker-track">
        <span>Made to Order</span><span class="ticker-dot">✦</span>
        <span>No Minimums</span><span class="ticker-dot">✦</span>
        <span>DTG + Embroidery</span><span class="ticker-dot">✦</span>
        <span>Ships in 5–7 Days</span><span class="ticker-dot">✦</span>
        <span>Free Shipping Over $100</span><span class="ticker-dot">✦</span>
        <span>Made to Order</span><span class="ticker-dot">✦</span>
        <span>No Minimums</span><span class="ticker-dot">✦</span>
        <span>DTG + Embroidery</span><span class="ticker-dot">✦</span>
        <span>Ships in 5–7 Days</span><span class="ticker-dot">✦</span>
        <span>Free Shipping Over $100</span><span class="ticker-dot">✦</span>
    </div>
</div>

<section class="products-section">
    <div class="container">
        <div class="section-header">
            <div>
                <span class="section-eyebrow">Start With a Blank</span>
                <h2>Customizable pieces</h2>
            </div>
            <a href="{{ route('shop') }}" class="view-all">View All &rarr;</a>
        </div>
        <div class="products-grid">
            @foreach($products as $product)
                <div class="product-card">
                    <a href="{{ route('shop.show', $product->slug) }}" class="product-card__link">
                        <div class="product-card__image">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
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
    </div>
</section>

<section class="how-it-works" id="how-it-works">
    <div class="container">
        <div class="how-it-works__header">
            <span class="section-eyebrow">How It Works</span>
            <h2>Three steps to one of a kind.</h2>
        </div>
        <div class="how-it-works__steps">
            <div class="step">
                <span class="step__number">01</span>
                <h3 class="step__title">Pick your blank</h3>
                <p class="step__desc">Tees, hoodies, caps, totes — premium garments in classic cuts.</p>
            </div>
            <div class="step">
                <span class="step__number">02</span>
                <h3 class="step__title">Add your design</h3>
                <p class="step__desc">Upload art, type a word, or work with our studio on something new.</p>
            </div>
            <div class="step">
                <span class="step__number">03</span>
                <h3 class="step__title">We make it</h3>
                <p class="step__desc">Printed or embroidered to order in our studio, then shipped to you.</p>
            </div>
        </div>
    </div>
</section>

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
