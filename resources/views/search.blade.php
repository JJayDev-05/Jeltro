@extends('layouts.jeltro')

@section('title', 'Search — Jeltro')

@section('content')

<div class="search-page">

    {{-- Search bar at top --}}
    <div class="search-page__bar-wrap">
        <div class="search-page__form-wrap">
            <form action="{{ route('shop') }}" method="GET" class="search-page__form" id="search-form">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="search-page__icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" name="q" id="search-input" placeholder="Search products..." class="search-page__input" autofocus autocomplete="off" />
            </form>
            <div id="search-suggestions" class="search-suggestions"></div>
        </div>
    </div>

    <hr class="search-page__divider">

    {{-- Men / Women tabs centered --}}
    <div class="search-page__gender-tabs">
        <button class="search-page__gender-btn is-active" onclick="switchGender('men', this)">Men</button>
        <button class="search-page__gender-btn" onclick="switchGender('women', this)">Women</button>
    </div>

    {{-- Category cards --}}
    <div id="search-page-men" class="search-page__categories">
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'T-Shirts & Sweats']) }}" class="search-page__card">
<span class="search-page__card-label">T-Shirts & Sweats</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'Shirts & Polo Shirts']) }}" class="search-page__card">
<span class="search-page__card-label">Shirts & Polo Shirts</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'Sweaters & Knitwear']) }}" class="search-page__card">
<span class="search-page__card-label">Sweaters & Knitwear</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'Outerwear']) }}" class="search-page__card">
<span class="search-page__card-label">Outerwear</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'Bottoms']) }}" class="search-page__card">
<span class="search-page__card-label">Bottoms</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'Loungewear']) }}" class="search-page__card">
<span class="search-page__card-label">Loungewear</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men', 'q' => 'Linen']) }}" class="search-page__card">
<span class="search-page__card-label">Linen</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Men']) }}" class="search-page__card">
<span class="search-page__card-label">All Men's</span>
        </a>
    </div>

    <div id="search-page-women" class="search-page__categories" style="display:none;">
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'T-Shirts & Sweats']) }}" class="search-page__card">
<span class="search-page__card-label">T-Shirts & Sweats</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Shirts, Blouses & Polo Shirts']) }}" class="search-page__card">
<span class="search-page__card-label">Shirts, Blouses & Polo Shirts</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Sweaters & Knitwear']) }}" class="search-page__card">
<span class="search-page__card-label">Sweaters & Knitwear</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Outerwear']) }}" class="search-page__card">
<span class="search-page__card-label">Outerwear</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Bottoms']) }}" class="search-page__card">
<span class="search-page__card-label">Bottoms</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Dresses & Skirts']) }}" class="search-page__card">
<span class="search-page__card-label">Dresses & Skirts</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Loungewear']) }}" class="search-page__card">
<span class="search-page__card-label">Loungewear</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women', 'q' => 'Linen']) }}" class="search-page__card">
<span class="search-page__card-label">Linen</span>
        </a>
        <a href="{{ route('shop', ['gender' => 'Women']) }}" class="search-page__card">
<span class="search-page__card-label">All Women's</span>
        </a>
    </div>

</div>

<script>
function switchGender(gender, btn) {
    document.querySelectorAll('.search-page__gender-btn').forEach(t => t.classList.remove('is-active'));
    btn.classList.add('is-active');
    document.getElementById('search-page-men').style.display = gender === 'men' ? 'grid' : 'none';
    document.getElementById('search-page-women').style.display = gender === 'women' ? 'grid' : 'none';
}

const searchInput = document.getElementById('search-input');
const suggestionsBox = document.getElementById('search-suggestions');
const shopUrl = '{{ route('shop') }}';
const suggestionsUrl = '{{ route('search.suggestions') }}';
let debounceTimer;

searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 2) {
        suggestionsBox.innerHTML = '';
        suggestionsBox.classList.remove('is-open');
        return;
    }
    debounceTimer = setTimeout(() => {
        fetch(`${suggestionsUrl}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(products => {
                if (!products.length) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.classList.remove('is-open');
                    return;
                }
                suggestionsBox.innerHTML = products.map(p => `
                    <a href="${shopUrl}?q=${encodeURIComponent(p.name)}" class="search-suggestion__item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="search-suggestion__icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <span class="search-suggestion__name">${p.name}</span>
                        ${p.category ? `<span class="search-suggestion__cat">${p.category}</span>` : ''}
                    </a>
                `).join('');
                suggestionsBox.classList.add('is-open');
            });
    }, 200);
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('.search-page__form-wrap')) {
        suggestionsBox.classList.remove('is-open');
    }
});
</script>

@endsection
