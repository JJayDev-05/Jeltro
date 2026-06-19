@extends('admin.layout')

@section('title', 'Archived Products')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <h1 style="font-size:20px;font-weight:600;margin:0;">Archived Products</h1>
    <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">← Back to Products</a>
</div>

<div class="admin-card" style="padding:0;">
    @if($products->isEmpty())
        <div style="padding:48px;text-align:center;color:#888;font-size:14px;">No archived products.</div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left:24px;">Product</th>
                    <th>Category</th>
                    <th>Gender</th>
                    <th>Price</th>
                    <th>Archived</th>
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
                                         style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid #e8e6e1;opacity:.6;">
                                @else
                                    <div style="width:44px;height:44px;border-radius:6px;background:#f0ede8;"></div>
                                @endif
                                <div>
                                    <p style="font-weight:600;margin:0 0 2px;color:#888;">{{ $product->name }}</p>
                                    <p style="font-size:12px;color:#bbb;margin:0;">{{ $product->slug }}</p>
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
                        <td style="font-size:13px;color:#999;">{{ $product->gender ?? '—' }}</td>
                        <td style="font-weight:600;color:#999;">${{ number_format($product->price, 2) }}</td>
                        <td style="font-size:12px;color:#bbb;">{{ $product->deleted_at->format('M d, Y') }}</td>
                        <td style="padding-right:24px;">
                            <div style="display:flex;align-items:center;gap:8px;justify-content:flex-end;">
                                <form method="POST" action="{{ route('admin.products.restore', $product->id) }}">
                                    @csrf
                                    <button type="submit" class="admin-btn admin-btn--outline admin-btn--sm">Restore</button>
                                </form>
                                <button type="button" class="admin-btn admin-btn--danger admin-btn--sm"
                                        data-name="{{ $product->name }}"
                                        data-action="{{ route('admin.products.force-delete', $product->id) }}"
                                        onclick="openDeleteModal(this.dataset.name, this.dataset.action)">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Permanent delete confirmation modal --}}
<div id="delete-modal" class="confirm-modal" onclick="if(event.target===this) closeDeleteModal()">
    <div class="confirm-modal__box">
        <div class="confirm-modal__icon" style="background:#fee2e2;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#b91c1c" style="width:22px;height:22px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </div>
        <p class="confirm-modal__title">Permanently delete?</p>
        <p class="confirm-modal__body">
            <strong id="delete-product-name"></strong> will be permanently removed and cannot be recovered.
        </p>
        <div class="confirm-modal__actions">
            <button type="button" class="admin-btn admin-btn--outline" onclick="closeDeleteModal()">Cancel</button>
            <form id="delete-form" method="POST">
                @csrf
                <button type="submit" class="admin-btn admin-btn--danger">Delete permanently</button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(name, action) {
    document.getElementById('delete-product-name').textContent = name;
    document.getElementById('delete-form').action = action;
    document.getElementById('delete-modal').classList.add('is-open');
}
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('is-open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

@endsection
