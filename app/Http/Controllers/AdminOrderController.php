<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\CancelRequestDecided;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');

        $orders = match($tab) {
            'pending'     => Order::where('status', 'pending')->latest()->get(),
            'on_delivery' => Order::where('status', 'on_delivery')->latest()->get(),
            'completed'   => Order::where('status', 'completed')->latest()->get(),
            'cancelled'   => Order::where('status', 'cancelled')->latest()->get(),
            default       => Order::latest()->get(),
        };

        $counts = [
            'pending'     => Order::where('status', 'pending')->count(),
            'on_delivery' => Order::where('status', 'on_delivery')->count(),
            'completed'   => Order::where('status', 'completed')->count(),
            'cancelled'   => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'tab', 'counts'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,on_delivery,completed,cancelled'],
        ]);

        $validTransitions = [
            'pending'     => ['on_delivery', 'cancelled'],
            'on_delivery' => ['completed', 'cancelled'],
            'completed'   => [],
            'cancelled'   => [],
        ];

        if (!in_array($request->status, $validTransitions[$order->status] ?? [])) {
            return back()->with('error', 'Invalid status transition from ' . $order->status . ' to ' . $request->status . '.');
        }

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        if ($oldStatus !== $request->status) {
            $order->user->notify(new OrderStatusChanged($order, $oldStatus));
        }

        return back()->with('success', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' updated to ' . $request->status . '.');
    }

    public function approveCancel(Order $order)
    {
        $oldStatus = $order->status;
        $order->update(['status' => 'cancelled', 'cancel_requested' => false]);
        $order->user->notify(new CancelRequestDecided($order, approved: true));

        return back()->with('success', 'Cancellation approved for Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '.');
    }

    public function rejectCancel(Order $order)
    {
        $order->update(['cancel_requested' => false]);
        $order->user->notify(new CancelRequestDecided($order, approved: false));

        return back()->with('success', 'Cancellation rejected for Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '.');
    }
}
