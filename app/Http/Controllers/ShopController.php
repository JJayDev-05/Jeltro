<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $products = Product::latest()->take(4)->get();
        $savedIds = auth()->check()
            ? auth()->user()->savedProducts()->pluck('products.id')->toArray()
            : [];
        return view('home', compact('products', 'savedIds'));
    }

    public function index(Request $request)
    {
        $query  = $request->query('q');
        $gender = $request->query('gender');

        $products = Product::query()
            ->when($query, fn($q) => $q->where(fn($q) => $q
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
            ))
            ->when($gender, fn($q) => $q->where('gender', $gender))
            ->get();

        $savedIds = auth()->check()
            ? auth()->user()->savedProducts()->pluck('products.id')->toArray()
            : [];

        return view('shop.index', compact('products', 'query', 'gender', 'savedIds'));
    }

    public function search()
    {
        return view('search');
    }

    public function suggestions(Request $request)
    {
        $q = $request->query('q', '');
        if (strlen($q) < 2) return response()->json([]);

        $products = Product::where('name', 'like', "%{$q}%")
            ->orWhere('category', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->take(8)
            ->get(['name', 'category', 'slug']);

        return response()->json($products);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $isSaved = auth()->check()
            ? auth()->user()->savedProducts()->where('products.id', $product->id)->exists()
            : false;
        return view('shop.show', compact('product', 'isSaved'));
    }
}
