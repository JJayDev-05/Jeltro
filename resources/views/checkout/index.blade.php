@extends('layouts.jeltro')

@section('title', 'Checkout — Jeltro')

@section('content')

<div class="checkout-page">
    <div class="container">

        <div class="checkout-page-header">
            <span class="section-eyebrow">Secure Checkout</span>
            <h1 class="checkout-page-title">Complete your order</h1>
        </div>

        <form method="POST" action="{{ route('checkout.store') }}" class="woocommerce-checkout">
            @csrf
            <div class="checkout-layout">

                <!-- Left: Customer details -->
                <div class="checkout-details">

                    <div class="woocommerce-billing-fields">
                        <h3>Billing Details</h3>

                        @if($address)
                            <div style="border:1px solid var(--border);border-radius:8px;padding:20px 24px;margin-top:16px;">
                                <p style="font-size:15px;font-weight:600;margin-bottom:4px;">{{ $address->name }}</p>
                                @if($address->phone)
                                    <p style="font-size:14px;color:#666;margin-bottom:2px;">+63 {{ $address->phone }}</p>
                                @endif
                                <p style="font-size:14px;color:#666;margin-bottom:2px;">{{ $address->address_1 }}</p>
                                @php
                                    $cityLine = collect([$address->barangay, $address->city])->filter()->implode(', ');
                                    $regionLine = collect([$address->province, $address->region])->filter()->implode(', ');
                                    if ($address->postcode) $regionLine .= ' ' . $address->postcode;
                                @endphp
                                @if($cityLine)<p style="font-size:14px;color:#666;margin-bottom:2px;">{{ $cityLine }}</p>@endif
                                @if($regionLine)<p style="font-size:14px;color:#666;margin-bottom:2px;">{{ $regionLine }}</p>@endif
                                <a href="{{ route('account') }}#addresses" style="display:inline-block;margin-top:12px;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);">Change address</a>
                            </div>
                        @else
                            <div style="border:1px solid var(--border);border-radius:8px;padding:20px 24px;margin-top:16px;">
                                <p style="font-size:14px;color:#666;margin-bottom:12px;">No saved address found.</p>
                                <a href="{{ route('account') }}#addresses" style="font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--accent);">Add an address</a>
                            </div>
                        @endif
                    </div>

                    <!-- Payment -->
                    <div id="payment" style="margin-top:32px;">
                        <ul class="wc_payment_methods">
                            <li class="wc_payment_method">
                                <input type="radio" name="payment_method" id="payment_method_demo" value="demo" checked style="accent-color:var(--text);width:16px;height:16px;">
                                <label for="payment_method_demo" style="display:flex;align-items:center;gap:10px;padding:14px 16px;cursor:pointer;font-size:13px;font-weight:500;">
                                    Demo Payment
                                </label>
                                <div class="payment_box">
                                    <p>This is a portfolio demo. No real payment will be processed.</p>
                                </div>
                            </li>
                        </ul>

                        <button type="submit" id="place_order" class="btn btn--dark">Place order</button>
                    </div>

                </div>

                <!-- Right: Order summary -->
                <div class="checkout-summary">
                    <h2 class="checkout-summary-title">Your order</h2>

                    <table class="woocommerce-checkout-review-order-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="text-align:right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cart as $item)
                                <tr class="cart_item">
                                    <td class="product-name">
                                        {{ $item['name'] }}
                                        <strong class="product-quantity"> × {{ $item['quantity'] }}</strong>
                                    </td>
                                    <td class="product-total">
                                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Subtotal</th>
                                <td>${{ number_format($total, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Shipping</th>
                                <td>{{ $total >= 100 ? 'Free' : '$10.00' }}</td>
                            </tr>
                            <tr class="order-total">
                                <th>Total</th>
                                <td>${{ number_format($total >= 100 ? $total : $total + 10, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection
