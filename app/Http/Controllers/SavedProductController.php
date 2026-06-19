<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SavedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedProductController extends Controller
{
    public function toggle(Product $product)
    {
        $user = Auth::user();
        $existing = SavedProduct::where('user_id', $user->id)
                                ->where('product_id', $product->id)
                                ->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
        } else {
            SavedProduct::create(['user_id' => $user->id, 'product_id' => $product->id]);
            $saved = true;
        }

        return response()->json(['saved' => $saved]);
    }
}
