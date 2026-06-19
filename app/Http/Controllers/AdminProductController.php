<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string', 'max:100'],
            'gender'      => ['required', 'in:Men,Women'],
            'stock'       => ['required', 'integer', 'min:0'],
            'sizes'       => ['nullable', 'string'],
            'colors'      => ['nullable', 'string'],
            'image'       => ['required', 'extensions:jpg,jpeg,png,gif,webp', 'max:2048'],
            'images.*'    => ['nullable', 'extensions:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $data['slug']   = Str::slug($data['name']) . '-' . Str::random(5);
        $data['sizes']  = $this->parseList($request->sizes);
        $data['colors'] = $this->parseList($request->colors);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $extraImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $extraImages[] = $file->store('products', 'public');
            }
        }
        $data['images'] = $extraImages;

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string', 'max:100'],
            'gender'      => ['required', 'in:Men,Women'],
            'stock'       => ['required', 'integer', 'min:0'],
            'sizes'       => ['nullable', 'string'],
            'colors'      => ['nullable', 'string'],
            'image'       => ['nullable', 'extensions:jpg,jpeg,png,gif,webp', 'max:2048'],
            'images.*'    => ['nullable', 'extensions:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $data['sizes']  = $this->parseList($request->sizes);
        $data['colors'] = $this->parseList($request->colors);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($data['image']);
        }

        $existingImages = $product->images ?? [];
        foreach ($request->input('remove_images', []) as $path) {
            $existingImages = array_values(array_diff($existingImages, [$path]));
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $existingImages[] = $file->store('products', 'public');
            }
        }
        $data['images'] = $existingImages;

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function archive(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product archived.');
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('admin.products.archived')->with('success', 'Product restored.');
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();
        return redirect()->route('admin.products.archived')->with('success', 'Product permanently deleted.');
    }

    public function archived()
    {
        $products = Product::onlyTrashed()->latest('deleted_at')->get();
        return view('admin.products.archived', compact('products'));
    }

    private function parseList(?string $value): array
    {
        if (!$value) return [];
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}
