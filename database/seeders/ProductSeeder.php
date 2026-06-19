<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Classic White Tee', 'slug' => 'classic-white-tee', 'description' => 'A clean, minimal white t-shirt made from 100% cotton.', 'price' => 29.99, 'category' => 'Tops', 'stock' => 50],
            ['name' => 'Black Slim Joggers', 'slug' => 'black-slim-joggers', 'description' => 'Comfortable slim-fit joggers perfect for everyday wear.', 'price' => 49.99, 'category' => 'Bottoms', 'stock' => 30],
            ['name' => 'Oversized Hoodie', 'slug' => 'oversized-hoodie', 'description' => 'Relaxed fit hoodie in a premium fleece blend.', 'price' => 64.99, 'category' => 'Tops', 'stock' => 25],
            ['name' => 'Cargo Shorts', 'slug' => 'cargo-shorts', 'description' => 'Utility cargo shorts with multiple pockets.', 'price' => 39.99, 'category' => 'Bottoms', 'stock' => 40],
            ['name' => 'Linen Button Shirt', 'slug' => 'linen-button-shirt', 'description' => 'Breathable linen shirt ideal for warm weather.', 'price' => 54.99, 'category' => 'Tops', 'stock' => 20],
            ['name' => 'Denim Jacket', 'slug' => 'denim-jacket', 'description' => 'Classic denim jacket with a modern slim cut.', 'price' => 89.99, 'category' => 'Outerwear', 'stock' => 15],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
