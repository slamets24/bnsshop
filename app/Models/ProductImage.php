<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
    ];

    protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        return $this->image_url ? asset('storage/' . $this->image_url) : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
