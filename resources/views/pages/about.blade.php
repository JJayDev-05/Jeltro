@extends('layouts.jeltro')

@section('title', 'About — Jeltro')

@section('content')

<section class="about-hero">
    <span class="section-eyebrow">How it works</span>
    <h1 class="about-hero__title">Your design, on garments built to last.</h1>
    <p class="about-hero__desc">Jeltro is a custom apparel studio. We print and embroider your designs on premium blanks, one piece at a time — so there are no minimums, no inventory waste, and no two orders quite alike.</p>
</section>

<section class="about-features">
    <div class="about-features__grid">
        <div class="about-feature">
            <h3 class="about-feature__title">Made to Order</h3>
            <p class="about-feature__desc">Every garment is printed or stitched after you order — nothing sits in a warehouse.</p>
        </div>
        <div class="about-feature">
            <h3 class="about-feature__title">Premium Blanks</h3>
            <p class="about-feature__desc">Heavyweight cotton tees, fleece hoodies, structured caps. The good stuff.</p>
        </div>
        <div class="about-feature">
            <h3 class="about-feature__title">DTG + Embroidery</h3>
            <p class="about-feature__desc">Direct-to-garment prints for full color, embroidery for clean type and logos.</p>
        </div>
    </div>
</section>

@endsection
