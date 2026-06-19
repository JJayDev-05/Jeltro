<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart));
        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('redirect_after', url()->previous());
        }

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        $designText = $request->input('design_text');
        $designFile = null;
        if ($request->hasFile('design_file')) {
            $request->validate([
                'design_file' => ['file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:5120'],
            ]);
            $designFile = $request->file('design_file')->store('designs', 'public');
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
            if ($designText) $cart[$product->id]['design_text'] = $designText;
            if ($designFile) $cart[$product->id]['design_file'] = $designFile;
        } else {
            $cart[$product->id] = [
                'name'        => $product->name,
                'price'       => $product->price,
                'quantity'    => 1,
                'image'       => $product->image,
                'slug'        => $product->slug,
                'design_text' => $designText ?: null,
                'design_file' => $designFile ?: null,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = max(1, (int) $request->quantity);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);
        return redirect()->route('cart.index');
    }
}
