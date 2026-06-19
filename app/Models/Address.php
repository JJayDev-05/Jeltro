<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'type', 'is_default', 'name', 'phone', 'address_1', 'region', 'province', 'city', 'barangay', 'postcode'];

    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
