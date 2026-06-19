@extends('layouts.jeltro')

@section('title', 'Order Placed — Jeltro')

@section('content')

<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:80px 24px;">
    <div style="max-width:560px;width:100%;text-align:center;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:56px;height:56px;color:var(--accent);margin:0 auto 24px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <h1 style="font-family:var(--font-heading);font-size:clamp(32px,4vw,52px);font-weight:500;margin-bottom:16px;">Order placed!</h1>
        <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:40px;">
            Thank you for your order. In a real store, you'd receive a confirmation email shortly.
            This is a portfolio demo — no real transaction was made.
        </p>
        <a href="{{ route('shop') }}" class="btn btn--dark">Continue shopping</a>
    </div>
</div>

@endsection
