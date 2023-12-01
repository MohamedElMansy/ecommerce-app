<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'is_vat_included', 'vat_price',
        'vat_percentage', 'shipping_cost', 'user_id'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
