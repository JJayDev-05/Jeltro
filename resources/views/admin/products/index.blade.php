@extends('admin.layout')

@section('title', 'Products')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <h1 style="font-size:20px;font-weight:600;margin:0;">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn--dark">+ Add Product</a>
</div>

<div class="admin-card" style="padding:0;">
    @if($products->isEmpty())
        <div style="padding:48px;text-align:center;color:#888;font-size:14px;">No products yet. <a href="{{ route('admin.products.create') }}" style="color:#1a1a1a;font-weight:600;">Add one</a>.</div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left:24px;">Product</th>
                    <th>Category</th>
                    <th>Gender</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sizes</th>
                    <th>Colors</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td style="padding-left:24px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                         style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid #e8e6e1;">
                                @else
                                    <div style="width:44px;height:44px;border-radius:6px;background:#f0ede8;"></div>
                                @endif
                                <div>
                                    <p style="font-weight:600;margin:0 0 2px;">{{ $product->name }}</p>
                                    <p style="font-size:12px;color:#888;margin:0;">{{ $product->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge badge--gray">{{ $product->category }}</span>
                            @else
                                <span style="color:#bbb;">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:#666;">{{ $product->gender ?? '—' }}</td>
                        <td style="font-weight:600;">${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->stock }}</td>
                        <td style="font-size:13px;color:#666;">
                            {{ $product->sizes ? implode(', ', $product->sizes) : '—' }}
                        </td>
                        <td style="font-size:13px;color:#666;">
                            {{ $product->colors ? implode(', ', $product->colors) : '—' }}
                        </td>
                        <td style="padding-right:24px;">
                            <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn admin-btn--outline admin-btn--sm">Edit</a>
                                <button type="button" class="admin-btn admin-btn--outline admin-btn--sm"
                                        data-name="{{ $product->name }}"
                                        data-action="{{ route('admin.products.archive', $product) }}"
                                        onclick="openArchiveModal(this.dataset.name, this.dataset.action)">
                                    Archive
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Archive confirmation modal --}}
<div id="archive-modal" class="confirm-modal" onclick="if(event.target===this) closeArchiveModal()">
    <div class="confirm-modal__box">
        <div class="confirm-modal__icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:22px;height:22px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-.375c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v.375c0 .621.504 1.125 1.125 1.125Z" />
            </svg>
        </div>
        <p class="confirm-modal__title">Archive product?</p>
        <p class="confirm-modal__body">
            <strong id="archive-product-name"></strong> will be hidden from the shop and product list. You can restore it anytime from Archived.
        </p>
        <div class="confirm-modal__actions">
            <button type="button" class="admin-btn admin-btn--outline" onclick="closeArchiveModal()">Cancel</button>
            <form id="archive-form" method="POST">
                @csrf
                <button type="submit" class="admin-btn admin-btn--dark">Archive</button>
            </form>
        </div>
    </div>
</div>

<script>
function openArchiveModal(name, action) {
    document.getElementById('archive-product-name').textContent = name;
    document.getElementById('archive-form').action = action;
    document.getElementById('archive-modal').classList.add('is-open');
}
function closeArchiveModal() {
    document.getElementById('archive-modal').classList.remove('is-open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeArchiveModal();
});
</script>

@endsection
