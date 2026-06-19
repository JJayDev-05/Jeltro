@extends('layouts.jeltro')

@section('title', $product->name . ' — Jeltro')

@section('content')

<div class="product-page">
    <div class="product-page__back container">
        <a href="{{ route('shop') }}">← Back to shop</a>
    </div>

    <div class="container">
        <div class="product-layout">

            <!-- Image Gallery -->
            @php
                $allImages = array_values(array_filter(array_merge(
                    $product->image ? [$product->image] : [],
                    $product->images ?? []
                )));
            @endphp
            <div class="product-gallery__wrap">

                {{-- Thumbnail strip (left side, same height as main, scrollable) --}}
                @if(count($allImages) > 0)
                    <div class="product-gallery__thumbs">
                        @foreach($allImages as $i => $img)
                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }} {{ $i + 1 }}"
                                 onclick="galleryGoto({{ $i }})"
                                 id="thumb-{{ $i }}"
                                 class="product-gallery__thumb {{ $i === 0 ? 'is-active' : '' }}">
                        @endforeach
                    </div>
                @endif

                {{-- Main image --}}
                <div class="product-gallery__main-wrap">
                    <div class="product-gallery__main">
                        @if(count($allImages) > 0)
                            <img id="gallery-main" src="{{ asset('storage/' . $allImages[0]) }}" alt="{{ $product->name }}" class="product-gallery__img" onclick="openLightbox(this.src)" style="cursor:zoom-in;">
                        @else
                            <div class="product-gallery__placeholder"></div>
                        @endif
                    </div>
                    @auth
                        <button class="product-save-btn {{ $isSaved ? 'is-saved' : '' }}"
                                data-url="{{ route('saved.toggle', $product) }}"
                                onclick="toggleSave(this)"
                                title="{{ $isSaved ? 'Remove from saved' : 'Save' }}">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                            </svg>
                        </button>
                    @endauth
                </div>

            </div>

            <!-- Info -->
            <div class="product-info">
                @if($product->category)
                    <span class="section-eyebrow">{{ $product->category }}</span>
                @endif

                <h1 class="product-info__title">{{ $product->name }}</h1>
                <p class="product-info__price">${{ number_format($product->price, 2) }}</p>

                @if($product->description)
                    <p class="product-info__desc">{{ $product->description }}</p>
                @endif

                @if($product->sizes && count($product->sizes) > 0)
                <div class="product-variants">
                    <span class="product-variants__label">
                        @if($product->gender === 'Women') Women's Size
                        @elseif($product->gender === 'Men') Men's Size
                        @else Size
                        @endif
                    </span>
                    <div class="product-variants__btns">
                        @foreach($product->sizes as $i => $size)
                            <button class="variant-btn {{ $i === 0 ? 'is-selected' : '' }}" type="button" data-variant="size">{{ $size }}</button>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($product->colors && count($product->colors) > 0)
                <div class="product-variants" style="margin-top:16px;">
                    <span class="product-variants__label">Color</span>
                    <div class="product-variants__btns">
                        @foreach($product->colors as $i => $color)
                            <button class="variant-btn {{ $i === 0 ? 'is-selected' : '' }}" type="button" data-variant="color">{{ $color }}</button>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- Add Your Design --}}
                        <div style="margin:28px 0;border-top:1px solid var(--border);padding-top:24px;">
                            <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);margin-bottom:18px;">Add Your Design</p>

                            <div style="margin-bottom:16px;">
                                <label style="font-size:13px;font-weight:600;color:var(--text);display:block;margin-bottom:8px;">Upload art</label>
                                <div id="upload-zone" onclick="document.getElementById('design-file').click()"
                                     style="border:1.5px dashed var(--border);border-radius:8px;padding:24px 16px;text-align:center;cursor:pointer;transition:border-color .2s;"
                                     onmouseenter="this.style.borderColor='var(--accent)'" onmouseleave="this.style.borderColor='var(--border)'">
                                    <input type="file" id="design-file" name="design_file" accept="image/*,.pdf,.ai,.eps" style="display:none;" onchange="handleUpload(this)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px;color:var(--text-muted);margin:0 auto 8px;display:block;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <p id="upload-label" style="font-size:13px;color:var(--text-muted);margin:0;">Click to upload — PNG, JPG, SVG, PDF</p>
                                </div>
                            </div>

                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                                <div style="flex:1;height:1px;background:var(--border);"></div>
                                <span style="font-size:12px;color:var(--text-muted);font-weight:500;">or</span>
                                <div style="flex:1;height:1px;background:var(--border);"></div>
                            </div>

                            <div>
                                <label style="font-size:13px;font-weight:600;color:var(--text);display:block;margin-bottom:8px;">Type a word or phrase</label>
                                <input type="text" name="design_text" placeholder="e.g. Team Jeltro, your name…"
                                       style="width:100%;box-sizing:border-box;padding:11px 14px;border:1px solid var(--border);border-radius:6px;font-size:14px;background:var(--bg);color:var(--text);outline:none;font-family:var(--font-body);">
                            </div>
                        </div>

                        <button type="submit" class="btn btn--dark product-atc-btn">Add to cart</button>
                    </form>
                @else
                    <button class="btn btn--dark product-atc-btn is-soldout" disabled>Sold out</button>
                @endif

                <div class="product-meta">
                    <p>Category: {{ $product->category ?? 'General' }}</p>
                    <p>{{ $product->stock > 0 ? 'In stock (' . $product->stock . ')' : 'Out of stock' }}</p>
                    <p>Free shipping on orders over $100</p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightbox" onclick="closeLightbox()">
    <button id="lightbox-close" onclick="closeLightbox()">&times;</button>
    <img id="lightbox-img" src="" alt="Full size" onclick="event.stopPropagation()">
</div>

<script>
const galleryImages =@json(array_values(array_filter(array_merge($product->image ? [asset('storage/' . $product->image)] : [], array_map(fn($i) => asset('storage/' . $i), $product->images ?? [])))));
let galleryIndex = 0;

function galleryGoto(index) {
    galleryIndex = index;
    document.getElementById('gallery-main').src = galleryImages[index];
    document.querySelectorAll('[id^="thumb-"]').forEach((t, i) => {
        t.classList.toggle('is-active', i === index);
    });
}
function galleryNext() { galleryGoto((galleryIndex + 1) % galleryImages.length); }
function galleryPrev() { galleryGoto((galleryIndex - 1 + galleryImages.length) % galleryImages.length); }

function syncThumbHeight() {
    const main = document.getElementById('gallery-main');
    const thumbs = document.querySelector('.product-gallery__thumbs');
    if (main && thumbs) {
        thumbs.style.maxHeight = main.offsetHeight + 'px';
    }
}
window.addEventListener('load', syncThumbHeight);
window.addEventListener('resize', syncThumbHeight);

function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('is-open');
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('is-open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') galleryNext();
    if (e.key === 'ArrowLeft') galleryPrev();
});

function handleUpload(input) {
    const label = document.getElementById('upload-label');
    if (input.files.length) {
        label.textContent = '✓ ' + input.files[0].name;
        label.style.color = 'var(--text)';
    }
}
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
document.querySelectorAll('.variant-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const group = btn.dataset.variant;
        document.querySelectorAll(`.variant-btn[data-variant="${group}"]`).forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
    });
});
</script>

@endsection
