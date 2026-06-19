@extends('admin.layout')

@section('title', 'Orders')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <h1 style="font-size:20px;font-weight:600;margin:0;">Orders</h1>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:4px;margin-bottom:24px;background:oklch(0.99 0.008 85);border:1px solid var(--border);border-radius:8px;padding:4px;width:fit-content;">
    @foreach([
        'pending'     => ['label' => 'Pending',      'color' => '#b45309'],
        'on_delivery' => ['label' => 'On Delivery',   'color' => '#1d6fa4'],
        'completed'   => ['label' => 'Completed',     'color' => '#15803d'],
        'cancelled'   => ['label' => 'Cancelled',     'color' => '#b91c1c'],
    ] as $key => $meta)
        <a href="{{ route('admin.orders.index', ['tab' => $key]) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border-radius:5px;font-size:12px;font-weight:600;letter-spacing:.04em;text-decoration:none;transition:background .15s;
                  {{ $tab === $key ? 'background:#1c1a14;color:#fff;' : 'color:var(--text-muted);' }}">
            {{ $meta['label'] }}
            @if($counts[$key] > 0)
                <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;font-size:10px;font-weight:700;
                             {{ $tab === $key ? 'background:rgba(255,255,255,.2);color:#fff;' : 'background:var(--bg-alt);color:var(--text-muted);' }}">
                    {{ $counts[$key] }}
                </span>
            @endif
        </a>
    @endforeach
</div>

<div class="admin-card" style="padding:0;">
    @if($orders->isEmpty())
        <div style="padding:48px;text-align:center;color:#888;font-size:14px;">No {{ str_replace('_', ' ', $tab) }} orders.</div>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left:24px;">Order</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td style="padding-left:24px;font-weight:600;">
                            #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                            @if($order->cancel_requested)
                                <span style="display:block;font-size:10px;font-weight:700;color:#b91c1c;letter-spacing:.04em;margin-top:2px;">&#9888; Cancel Requested</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">{{ $order->shipping_name ?? '—' }}</td>
                        <td style="font-size:13px;color:var(--text-muted);">{{ $order->created_at->format('M d, Y') }}</td>
                        <td style="font-size:13px;color:var(--text-muted);">
                            <button type="button" onclick="toggleDetails({{ $order->id }})"
                                    style="all:unset;cursor:pointer;font-size:13px;color:var(--accent);font-weight:600;text-decoration:underline;text-underline-offset:2px;">
                                {{ count($order->items) }} item{{ count($order->items) !== 1 ? 's' : '' }}
                            </button>
                        </td>
                        <td style="font-weight:600;">${{ number_format($order->total, 2) }}</td>
                        <td>
                            @php
                                $pill = match($order->status) {
                                    'pending'     => ['bg' => '#fef3c7', 'color' => '#b45309'],
                                    'on_delivery' => ['bg' => '#dbeafe', 'color' => '#1d6fa4'],
                                    'completed'   => ['bg' => '#dcfce7', 'color' => '#15803d'],
                                    'cancelled'   => ['bg' => '#fee2e2', 'color' => '#b91c1c'],
                                    default       => ['bg' => '#f0ede8', 'color' => '#666'],
                                };
                            @endphp
                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $pill['bg'] }};color:{{ $pill['color'] }};">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td style="padding-right:24px;">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                @php $orderNo = str_pad($order->id, 4, '0', STR_PAD_LEFT); @endphp
                                @if($order->status === 'pending')
                                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="on_delivery">
                                        <button type="button" class="admin-btn admin-btn--dark admin-btn--sm"
                                                data-message="Mark order #{{ $orderNo }} as On the Way?"
                                                onclick="openConfirm(this.closest('form'), this.dataset.message)">&#128666; On the Way</button>
                                    </form>
                                @elseif($order->status === 'on_delivery')
                                    <form method="POST" action="{{ route('admin.orders.status', $order) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="button" class="admin-btn admin-btn--dark admin-btn--sm"
                                                data-message="Mark order #{{ $orderNo }} as Completed?"
                                                onclick="openConfirm(this.closest('form'), this.dataset.message)">&#10003; Complete Order</button>
                                    </form>
                                @endif
                                @if($order->cancel_requested)
                                    <div style="display:flex;align-items:center;gap:6px;margin-left:16px;">
                                        <form method="POST" action="{{ route('admin.orders.cancel-approve', $order) }}">
                                            @csrf @method('PATCH')
                                            <button type="button" class="admin-btn admin-btn--danger admin-btn--sm"
                                                    data-message="Approve cancellation for order #{{ $orderNo }}? This cannot be undone."
                                                    onclick="openConfirm(this.closest('form'), this.dataset.message)">Approve Cancel</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.orders.cancel-reject', $order) }}">
                                            @csrf @method('PATCH')
                                            <button type="button" class="admin-btn admin-btn--outline admin-btn--sm"
                                                    data-message="Reject the cancellation request for order #{{ $orderNo }}?"
                                                    onclick="openConfirm(this.closest('form'), this.dataset.message)">Reject</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    {{-- Expandable item details --}}
                    <tr id="details-{{ $order->id }}" style="display:none;">
                        <td colspan="7" style="padding:0 24px 20px;">
                            <div style="background:var(--bg-alt);border:1px solid var(--border);border-radius:8px;padding:16px 20px;">
                                <p style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--text-muted);margin-bottom:12px;">Order Items</p>
                                @foreach($order->items as $item)
                                    <div style="display:flex;align-items:flex-start;gap:16px;padding:12px 0;border-bottom:1px solid var(--border);last:border-bottom:none;">
                                        @if(!empty($item['image']))
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                                 style="width:52px;height:52px;object-fit:cover;border-radius:6px;flex-shrink:0;">
                                        @endif
                                        <div style="min-width:320px;max-width:400px;">
                                            <p style="font-size:13px;font-weight:600;">{{ $item['name'] }}</p>
                                            <p style="font-size:12px;color:var(--text-muted);">Qty: {{ $item['quantity'] }} &bull; ${{ number_format($item['price'], 2) }}</p>
                                            @if(!empty($item['design_text']))
                                                <p style="font-size:12px;margin-top:6px;"><span style="font-weight:600;color:var(--text);">Note:</span> "{{ $item['design_text'] }}"</p>
                                            @endif
                                        </div>
                                        @if(!empty($item['design_file']))
                                            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;flex-shrink:0;">
                                                <img src="{{ asset('storage/' . $item['design_file']) }}" alt="Design"
                                                     style="width:72px;height:72px;object-fit:contain;border:1px solid var(--border);border-radius:6px;background:#fff;padding:4px;">
                                                @php
                                                    $ext = pathinfo($item['design_file'], PATHINFO_EXTENSION);
                                                    $customerName = str_replace(' ', '-', $order->shipping_name ?? $order->user->name ?? 'Customer');
                                                    $orderNo = str_pad($order->id, 4, '0', STR_PAD_LEFT);
                                                    $downloadName = "Order-{$orderNo}-{$customerName}.{$ext}";
                                                @endphp
                                                <a href="{{ asset('storage/' . $item['design_file']) }}" download="{{ $downloadName }}"
                                                   style="font-size:11px;font-weight:600;color:var(--accent);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                                    &#8659; Download
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Confirm Modal --}}
<div id="confirm-modal" style="display:none;position:fixed;inset:0;background:rgba(28,26,20,.5);backdrop-filter:blur(3px);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:36px 32px;width:400px;box-shadow:0 24px 64px rgba(0,0,0,.15);">
        <p style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);margin-bottom:10px;">Confirm Action</p>
        <p id="confirm-message" style="font-size:15px;font-weight:500;margin-bottom:28px;color:var(--text);line-height:1.5;font-family:var(--font-body);"></p>
        <div style="display:flex;flex-direction:row;gap:32px;justify-content:center;align-items:center;margin-top:8px;">
            <button type="button" onclick="closeConfirm()" class="admin-btn admin-btn--outline" style="margin:0;">Cancel</button>
            <button type="button" onclick="submitConfirm()" class="admin-btn admin-btn--dark" style="margin:0;">Confirm</button>
        </div>
    </div>
</div>

<script>
function toggleDetails(id) {
    const row = document.getElementById('details-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

let pendingForm = null;

function openConfirm(form, message) {
    pendingForm = form;
    document.getElementById('confirm-message').textContent = message;
    const modal = document.getElementById('confirm-modal');
    modal.style.display = 'flex';
}

function closeConfirm() {
    pendingForm = null;
    document.getElementById('confirm-modal').style.display = 'none';
}

function submitConfirm() {
    if (pendingForm) pendingForm.submit();
}

document.getElementById('confirm-modal').addEventListener('click', function(e) {
    if (e.target === this) closeConfirm();
});
</script>

@endsection
