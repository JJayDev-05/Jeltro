@extends('layouts.jeltro')

@section('title', 'Cart — Jeltro')

@section('content')

<div class="shop-page">
    <div class="container">
        <div class="shop-header">
            <span class="section-eyebrow">Your</span>
            <h1 class="shop-header__title">Shopping Cart</h1>
            @if(count($cart) > 0)
                <p class="shop-header__count">{{ array_sum(array_column($cart, 'quantity')) }} item(s)</p>
            @endif
        </div>

        @if(empty($cart))
            <div class="shop-empty">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
                <h2 class="shop-empty__title">Your cart is empty</h2>
                <p class="shop-empty__desc">Add some products to your cart to get started.</p>
                <a href="{{ route('shop') }}" class="btn btn--dark cart-empty__btn">Continue shopping</a>
            </div>
        @else
            <div class="cart-layout">

                <!-- Items -->
                <div class="cart-items">
                    @foreach($cart as $id => $item)
                        <div class="cart-item">
                            <div class="cart-item__image">
                                @if($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}">
                                @endif
                            </div>
                            <div class="cart-item__details cart-item__details--row">
                                <div>
                                    <p class="cart-item__title">{{ $item['name'] }}</p>
                                    <p class="cart-item__price">${{ number_format($item['price'], 2) }}</p>
                                    @if(!empty($item['design_text']))
                                        <p class="cart-item__design-text">✏ "{{ $item['design_text'] }}"</p>
                                    @endif

                                    <div class="cart-item__controls">
                                        <form method="POST" action="{{ route('cart.update', $id) }}">
                                            @csrf @method('PATCH')
                                            <div class="cart-qty">
                                                <button type="submit" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}" class="cart-qty__btn">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                </button>
                                                <span class="cart-qty__count">{{ $item['quantity'] }}</span>
                                                <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="cart-qty__btn">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if(!empty($item['design_file']))
                                    <div class="cart-item__design-file">
                                        <p class="cart-item__design-label">Design</p>
                                        <img src="{{ asset('storage/' . $item['design_file']) }}" alt="Design" class="cart-item__design-img">
                                    </div>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('cart.remove', $id) }}" class="cart-item__remove-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="cart-remove-btn" title="Remove">&times;</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="checkout-summary">
                    <h2 class="checkout-summary-title">Order Summary</h2>

                    <div class="cart-subtotal">
                        <span>Subtotal</span>
                        <span class="cart-subtotal__price">${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="cart-subtotal cart-subtotal--shipping">
                        <span>Shipping</span>
                        <span>{{ $total >= 100 ? 'Free' : '$10.00' }}</span>
                    </div>
                    <div class="cart-subtotal cart-subtotal--total">
                        <span>Total</span>
                        <span class="cart-subtotal__price">${{ number_format($total >= 100 ? $total : $total + 10, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn--dark cart-checkout-btn">
                        Proceed to checkout
                    </a>
                    <a href="{{ route('shop') }}" class="btn--link cart-continue-link">Continue shopping</a>
                </div>

            </div>
        @endif
    </div>
</div>

@endsection
