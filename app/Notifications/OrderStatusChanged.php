<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $oldStatus) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = match($this->order->status) {
            'on_delivery' => 'Your order is now on its way!',
            'completed'   => 'Your order has been delivered.',
            'cancelled'   => 'Your order has been cancelled.',
            default       => 'Your order status has been updated to ' . ucfirst(str_replace('_', ' ', $this->order->status)) . '.',
        };

        return [
            'order_id' => $this->order->id,
            'order_no' => str_pad($this->order->id, 4, '0', STR_PAD_LEFT),
            'status'   => $this->order->status,
            'message'  => $message,
        ];
    }
}
