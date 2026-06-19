<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        $user = Auth::user();
        assert($user instanceof User);
        $address = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->oldest()->first();
        return view('checkout.index', compact('cart', 'total', 'address'));
    }

    public function store()
    {
        $user = Auth::user();
        assert($user instanceof User);
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        // Re-fetch products from DB to validate stock and use current prices
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $id => $item) {
            $product = $products->get($id);
            if (!$product) {
                return redirect()->route('cart.index')
                    ->with('error', $item['name'] . ' is no longer available.');
            }
            if ($product->stock < $item['quantity']) {
                return redirect()->route('cart.index')
                    ->with('error', 'Not enough stock for ' . $product->name . '. Only ' . $product->stock . ' left.');
            }
        }

        // Recalculate subtotal from DB prices to prevent session manipulation
        $subtotal = 0;
        foreach ($cart as $id => $item) {
            $subtotal += $products->get($id)->price * $item['quantity'];
        }

        $shipping = $subtotal >= 100 ? 0 : 10;
        $address = $user->addresses()->where('is_default', true)->first()
                ?? $user->addresses()->oldest()->first();

        Order::create([
            'user_id'            => $user->id,
            'status'             => 'pending',
            'items'              => $cart,
            'subtotal'           => $subtotal,
            'shipping'           => $shipping,
            'total'              => $subtotal + $shipping,
            'estimated_delivery' => now()->addDays(7),
            'shipping_name'    => $address?->name,
            'shipping_address' => $address ? implode("\n", array_filter([
                $address->phone ? '+63 ' . $address->phone : null,
                $address->address_1,
                collect([$address->barangay, $address->city])->filter()->implode(', '),
                collect([$address->province, $address->region])->filter()->implode(', ') . ($address->postcode ? ' ' . $address->postcode : ''),
            ])) : null,
        ]);

        session()->forget('cart');
        return redirect()->route('checkout.success');
    }

    public function success()
    {
        return view('checkout.success');
    }
}
