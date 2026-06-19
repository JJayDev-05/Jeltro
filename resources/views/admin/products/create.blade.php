@extends('admin.layout')

@section('title', 'Add Product')

@section('content')

<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
    <a href="{{ route('admin.products.index') }}" style="font-size:20px;font-weight:600;color:#888;text-decoration:none;"><</a>
    <h1 style="font-size:20px;font-weight:600;margin:0;">Add Product</h1>
</div>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;">

        <!-- Left column -->
        <div>
            <div class="admin-card" style="margin-bottom:20px;">
                <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin:0 0 20px;">Product Info</h2>

                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                    @error('name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="4" style="resize:vertical;">{{ old('description') }}</textarea>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Price *</label>
                        <input type="number" name="price" class="form-input" value="{{ old('price') }}" step="0.01" min="0" required>
                        @error('price')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock *</label>
                        <input type="number" name="stock" class="form-input" value="{{ old('stock', 0) }}" min="0" required>
                        @error('stock')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin:0 0 20px;">Variants</h2>

                <div class="form-group">
                    <label class="form-label">Sizes</label>
                    <input type="text" name="sizes" class="form-input" value="{{ old('sizes') }}" placeholder="S, M, L, XL, XXL">
                    <p class="form-hint">Separate with commas</p>
                    @error('sizes')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Colors</label>
                    <input type="text" name="colors" class="form-input" value="{{ old('colors') }}" placeholder="Red, Blue, Black, White">
                    <p class="form-hint">Separate with commas</p>
                    @error('colors')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Right column -->
        <div>
            <div class="admin-card" style="margin-bottom:20px;">
                <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin:0 0 20px;">Category</h2>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <div style="display:flex;gap:24px;margin-top:6px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="radio" name="gender" value="Men" {{ old('gender') == 'Men' ? 'checked' : '' }} onchange="updateCategoryOptions(this.value)" required>
                            Men
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:14px;">
                            <input type="radio" name="gender" value="Women" {{ old('gender') == 'Women' ? 'checked' : '' }} onchange="updateCategoryOptions(this.value)" required>
                            Women
                        </label>
                    </div>
                    @error('gender')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Type *</label>
                    <select name="category" id="category-select" class="form-input" disabled required>
                        <option value="">— Select gender first —</option>
                    </select>
                    @error('category')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="admin-card">
                <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin:0 0 20px;">Product Images</h2>
                <div class="form-group">
                    <label class="form-label">Main Image *</label>
                    <input type="file" name="image" accept="image/*" class="form-input" style="padding:8px 14px;" required>
                    <p class="form-hint">First image shown on shop & cart</p>
                    @error('image')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Additional Images</label>
                    <input type="file" name="images[]" accept="image/*" multiple class="form-input" style="padding:8px 14px;" onchange="previewNewImages(this)">
                    <p class="form-hint">JPG, PNG, WEBP — max 2MB each. You can select multiple.</p>
                    <div id="new-previews" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
                </div>
            </div>
        </div>

    </div>

    <div style="margin-top:24px;display:flex;gap:12px;">
        <button type="submit" class="admin-btn admin-btn--dark">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn--outline">Cancel</a>
    </div>

</form>

<script>
function previewNewImages(input) {
    const container = document.getElementById('new-previews');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style = 'width:72px;height:72px;object-fit:cover;border-radius:6px;border:1px solid var(--border);';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}
</script>

<script>
const categoryOptions = {
    Men:   ['T-Shirts & Sweats', 'Shirts & Polo Shirts', 'Sweaters & Knitwear', 'Outerwear', 'Bottoms', 'Loungewear', 'Linen'],
    Women: ['T-Shirts & Sweats', 'Shirts, Blouses & Polo Shirts', 'Sweaters & Knitwear', 'Outerwear', 'Bottoms', 'Dresses & Skirts', 'Loungewear', 'Linen'],
};

function updateCategoryOptions(gender) {
    const select = document.getElementById('category-select');
    const current = select.dataset.current || '';
    select.disabled = false;
    select.innerHTML = '<option value="">— Select a type —</option>';
    (categoryOptions[gender] || []).forEach(opt => {
        const el = document.createElement('option');
        el.value = opt;
        el.textContent = opt;
        if (opt === current) el.selected = true;
        select.appendChild(el);
    });
}

// Init on page load if gender already selected (e.g. validation fail old value)
const checkedGender = document.querySelector('input[name="gender"]:checked');
if (checkedGender) updateCategoryOptions(checkedGender.value);
</script>
@endsection
