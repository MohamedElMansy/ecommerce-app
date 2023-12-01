<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['en_name', 'ar_name', 'description', 'price', 'store_id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
