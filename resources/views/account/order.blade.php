@extends('layouts.jeltro')

@section('title', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' — Jeltro')

@section('content')

<div class="container" style="max-width:860px;padding:60px 24px;">

    <a href="{{ route('account') }}#orders" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text);margin-bottom:32px;">
        &#8592; Back to account
    </a>

    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:8px;">
        <h1 style="font-family:var(--font-display);font-size:32px;font-weight:400;">Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1>
        <span style="display:inline-block;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;background:var(--bg-alt);border:1px solid var(--border);text-transform:capitalize;">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
    </div>
    <p style="font-size:14px;color:#888;margin-bottom:16px;">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>

    @if(session('success'))
        <div style="background:#f0fff4;border-left:3px solid #27ae60;padding:12px 16px;font-size:13px;color:#1a5c35;margin-bottom:20px;border-radius:4px;">
            {{ session('success') }}
        </div>
    @endif

    @if($order->status === 'pending' && !$order->cancel_requested)
        <div style="margin-bottom:24px;">
            <form id="cancel-request-form" method="POST" action="{{ route('account.order.cancel-request', $order) }}">
                @csrf
                <button type="button"
                        onclick="document.getElementById('cancel-modal').style.display='flex'"
                        style="all:unset;cursor:pointer;font-size:12px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:#b91c1c;text-decoration:underline;text-underline-offset:3px;">
                    Request Cancellation
                </button>
            </form>
        </div>

        {{-- Cancel Request Modal --}}
        <div id="cancel-modal" style="display:none;position:fixed;inset:0;background:rgba(28,26,20,.5);backdrop-filter:blur(3px);z-index:1000;align-items:center;justify-content:center;">
            <div style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:36px 32px;width:400px;box-shadow:0 24px 64px rgba(0,0,0,.15);">
                <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#b91c1c;margin-bottom:10px;">Request Cancellation</p>
                <p style="font-size:15px;font-weight:500;margin-bottom:28px;color:var(--text);line-height:1.5;">The admin will review your request.</p>
                <div style="display:flex;gap:32px;justify-content:center;align-items:center;margin-top:8px;">
                    <button type="button" onclick="document.getElementById('cancel-modal').style.display='none'"
                            style="all:unset;cursor:pointer;font-size:13px;font-weight:600;padding:9px 18px;border-radius:4px;border:1px solid var(--border);color:var(--text);">Keep Order</button>
                    <button type="button" onclick="document.getElementById('cancel-request-form').submit()"
                            style="all:unset;cursor:pointer;font-size:13px;font-weight:600;padding:9px 18px;border-radius:4px;background:#b91c1c;color:#fff;">Yes, Request</button>
                </div>
            </div>
        </div>
        <script>
        document.getElementById('cancel-modal').addEventListener('click', function(e) {
            if (e.target === this) this.style.display = 'none';
        });
        </script>
    @elseif($order->cancel_requested)
        <div style="background:#fef3c7;border-left:3px solid #b45309;padding:12px 16px;font-size:13px;color:#92400e;margin-bottom:24px;border-radius:4px;">
            &#9888; Your cancellation request is under review.
        </div>
    @endif

    {{-- Estimated delivery + status timeline --}}
    @php
        $steps = [
            'pending'     => ['Order Placed', 'Being Prepared', 'On Delivery', 'Delivered'],
            'on_delivery' => ['Order Placed', 'Being Prepared', 'On Delivery', 'Delivered'],
            'completed'   => ['Order Placed', 'Being Prepared', 'On Delivery', 'Delivered'],
            'cancelled'   => ['Order Placed', 'Cancelled'],
        ];
        $activeStep = match($order->status) {
            'pending'     => 1,
            'on_delivery' => 3,
            'completed'   => 4,
            'cancelled'   => 2,
            default       => 1,
        };
        $isCancelled = $order->status === 'cancelled';
        $currentSteps = $steps[$order->status] ?? $steps['pending'];
    @endphp

    <div style="border:1px solid var(--border);border-radius:12px;padding:24px 28px;margin-bottom:40px;background:var(--bg-alt);">
        {{-- Estimated delivery --}}
        @if(!$isCancelled)
            @php
                $deliveryStart = $order->estimated_delivery
                    ? $order->estimated_delivery->subDays(2)
                    : $order->created_at->addDays(5);
                $deliveryEnd = $order->estimated_delivery
                    ? $order->estimated_delivery
                    : $order->created_at->addDays(7);
            @endphp
            <div style="margin-bottom:28px;">
                <p style="font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);margin-bottom:4px;">Estimated Delivery</p>
                <p style="font-size:24px;font-family:var(--font-heading);font-weight:500;color:var(--text);">
                    {{ $deliveryStart->format('M d') }} – {{ $deliveryEnd->format('M d, Y') }}
                </p>
                @if($order->status === 'completed')
                    <p style="font-size:13px;color:var(--accent);font-weight:600;margin-top:6px;">&#10003; Your order has been delivered</p>
                @elseif($order->status === 'on_delivery')
                    <p style="font-size:13px;color:var(--text);font-weight:600;margin-top:6px;">Your order is on its way!</p>
                @else
                    <p style="font-size:13px;color:var(--text-muted);margin-top:6px;">We'll update you as your order progresses.</p>
                @endif
            </div>
        @else
            <p style="font-size:15px;font-weight:600;color:#b91c1c;margin-bottom:20px;">This order has been cancelled.</p>
        @endif

        {{-- Progress timeline --}}
        <div style="display:flex;align-items:center;gap:0;">
            @foreach($currentSteps as $i => $label)
                @php $stepNum = $i + 1; $done = $stepNum <= $activeStep; @endphp
                <div style="display:flex;align-items:center;flex:{{ $i < count($currentSteps) - 1 ? '1' : '0' }};">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;min-width:80px;">
                        <div style="width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;
                                    background:{{ $done ? ($isCancelled && $stepNum === 2 ? '#b91c1c' : 'var(--text)') : 'transparent' }};
                                    color:{{ $done ? '#fff' : 'var(--text-muted)' }};
                                    border:2px solid {{ $done ? ($isCancelled && $stepNum === 2 ? '#b91c1c' : 'var(--text)') : 'var(--border)' }};">
                            @if($done) &#10003; @else {{ $stepNum }} @endif
                        </div>
                        <span style="font-size:11px;font-weight:600;text-align:center;white-space:nowrap;color:{{ $done ? 'var(--text)' : 'var(--text-muted)' }};">{{ $label }}</span>
                    </div>
                    @if($i < count($currentSteps) - 1)
                        <div style="flex:1;height:2px;background:{{ $stepNum < $activeStep ? 'var(--text)' : 'var(--border)' }};margin-bottom:20px;"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;">

        <!-- Order items -->
        <div style="grid-column:span 2;">
            <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px;">Items ordered</h2>
            <table style="width:100%;border-collapse:collapse;font-size:14px;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border);text-align:left;">
                        <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;">Product</th>
                        <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;text-align:center;">Qty</th>
                        <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;text-align:right;">Price</th>
                        <th style="padding:10px 0;font-weight:600;text-transform:uppercase;font-size:12px;letter-spacing:.06em;text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:16px 0;">
                                <div style="display:flex;align-items:center;gap:14px;">
                                    @if(!empty($item['image']))
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                             style="width:56px;height:56px;object-fit:cover;border-radius:4px;">
                                    @endif
                                    <span style="font-weight:500;">{{ $item['name'] }}</span>
                                </div>
                            </td>
                            <td style="padding:16px 0;text-align:center;color:#666;">{{ $item['quantity'] }}</td>
                            <td style="padding:16px 0;text-align:right;color:#666;">${{ number_format($item['price'], 2) }}</td>
                            <td style="padding:16px 0;text-align:right;font-weight:600;">${{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding:12px 0;text-align:right;font-size:13px;color:#666;">Subtotal</td>
                        <td style="padding:12px 0;text-align:right;">${{ number_format($order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding:4px 0;text-align:right;font-size:13px;color:#666;">Shipping</td>
                        <td style="padding:4px 0;text-align:right;">{{ $order->shipping == 0 ? 'Free' : '$' . number_format($order->shipping, 2) }}</td>
                    </tr>
                    <tr style="border-top:2px solid var(--border);">
                        <td colspan="3" style="padding:14px 0;text-align:right;font-weight:600;font-size:15px;">Total</td>
                        <td style="padding:14px 0;text-align:right;font-weight:700;font-size:15px;">${{ number_format($order->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Shipping info -->
        @if($order->shipping_name || $order->shipping_address)
        <div>
            <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">Shipping to</h2>
            <div style="border:1px solid var(--border);border-radius:8px;padding:18px 20px;">
                @if($order->shipping_name)
                    <p style="font-weight:600;margin-bottom:4px;">{{ $order->shipping_name }}</p>
                @endif
                @if($order->shipping_address)
                    @foreach(explode("\n", $order->shipping_address) as $line)
                        <p style="font-size:14px;color:#666;margin-bottom:2px;">{{ $line }}</p>
                    @endforeach
                @endif
            </div>
        </div>
        @endif

        <!-- Payment info -->
        <div>
            <h2 style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">Payment</h2>
            <div style="border:1px solid var(--border);border-radius:8px;padding:18px 20px;">
                <p style="font-size:14px;color:#666;">Demo payment — no charge was made.</p>
            </div>
        </div>

    </div>

</div>

@endsection
