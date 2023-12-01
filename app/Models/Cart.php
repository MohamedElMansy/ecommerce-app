<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['key','user_id'];

    public function cartItems () {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
