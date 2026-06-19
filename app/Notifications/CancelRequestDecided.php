<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CancelRequestDecided extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public bool $approved) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $orderNo = str_pad($this->order->id, 4, '0', STR_PAD_LEFT);

        return [
            'order_id' => $this->order->id,
            'order_no' => $orderNo,
            'status'   => $this->order->status,
            'message'  => $this->approved
                ? "Your cancellation request for order #{$orderNo} has been approved. Your order is now cancelled."
                : "Your cancellation request for order #{$orderNo} was rejected. Your order is still being processed.",
        ];
    }
}
