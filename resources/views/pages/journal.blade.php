@extends('layouts.jeltro')

@section('title', 'Journal — Jeltro')

@section('content')

<section class="journal-hero">
    <span class="section-eyebrow" style="color:var(--text-muted);">Journal</span>
    <h1 class="journal-hero__title">From the studio</h1>
    <p class="journal-hero__desc">Stories, process notes, and the thinking behind what we make.</p>
</section>

<div class="container" style="max-width:860px;padding:60px 24px;">
    <div style="display:flex;flex-direction:column;gap:48px;">

        {{-- Article 1 --}}
        <article style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;padding-bottom:48px;border-bottom:1px solid var(--border);">
            <div style="background:var(--bg-alt);border-radius:8px;aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:48px;">🧵</span>
            </div>
            <div>
                <span style="font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);">Process</span>
                <h2 style="font-family:var(--font-heading);font-size:26px;font-weight:400;margin:10px 0 14px;line-height:1.3;">Embroidery vs. DTG, Which one is right for your design?</h2>
                <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">Both techniques produce stunning results, but they serve very different aesthetics. Embroidery adds texture and a premium feel perfect for logos and clean type. DTG handles full-color artwork and gradients without limits. Here's how we decide which method fits your vision.</p>
                <span style="font-size:12px;color:var(--text-muted);">June 10, 2026 &bull; 4 min read</span>
            </div>
        </article>

        {{-- Article 2 --}}
        <article style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;padding-bottom:48px;border-bottom:1px solid var(--border);">
            <div>
                <span style="font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);">Behind the Pieces</span>
                <h2 style="font-family:var(--font-heading);font-size:26px;font-weight:400;margin:10px 0 14px;line-height:1.3;">Why we only work with heavyweight blanks</h2>
                <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">A great print can't save a flimsy shirt. From the start, Jeltro committed to garments that hold their shape, feel substantial in hand, and age well. We walk you through the fabrics we've tested, rejected, and ultimately chosen and why weight and construction matter more than the brand on the tag.</p>
                <span style="font-size:12px;color:var(--text-muted);">May 28, 2026 &bull; 5 min read</span>
            </div>
            <div style="background:var(--bg-alt);border-radius:8px;aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:48px;">👕</span>
            </div>
        </article>

        {{-- Article 3 --}}
        <article style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;padding-bottom:48px;border-bottom:1px solid var(--border);">
            <div style="background:var(--bg-alt);border-radius:8px;aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:48px;">🎨</span>
            </div>
            <div>
                <span style="font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);">Design Tips</span>
                <h2 style="font-family:var(--font-heading);font-size:26px;font-weight:400;margin:10px 0 14px;line-height:1.3;">How to prepare your artwork for the best print results</h2>
                <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">File format, resolution, and color mode all affect the final print more than most people expect. A 300 DPI PNG on a transparent background prints differently than a low-res JPEG with a white fill. We break down exactly how to set up your file whether you're using Photoshop, Illustrator, or Canva.</p>
                <span style="font-size:12px;color:var(--text-muted);">May 14, 2026 &bull; 6 min read</span>
            </div>
        </article>

        {{-- Article 4 --}}
        <article style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;">
            <div>
                <span style="font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);">Studio Notes</span>
                <h2 style="font-family:var(--font-heading);font-size:26px;font-weight:400;margin:10px 0 14px;line-height:1.3;">One piece at a time why, we'll never do bulk orders</h2>
                <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">Most print shops push you toward bulk because it's more efficient for them. We built Jeltro around the opposite idea that a single, well-made piece for one person is worth more than a hundred identical shirts in a warehouse. No minimums isn't just a selling point. It's a philosophy.</p>
                <span style="font-size:12px;color:var(--text-muted);">April 30, 2026 &bull; 3 min read</span>
            </div>
            <div style="background:var(--bg-alt);border-radius:8px;aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
                <span style="font-size:48px;">📦</span>
            </div>
        </article>

    </div>
</div>

@endsection
