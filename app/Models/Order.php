<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'cancel_requested', 'items', 'subtotal', 'shipping', 'total', 'estimated_delivery', 'shipping_name', 'shipping_address'];
    protected $casts = ['items' => 'array', 'estimated_delivery' => 'date', 'cancel_requested' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
