<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function decreaseStock($quantity)
    {
        $this->decrement('stock', $quantity);
    }

    public function increaseStock($quantity)
    {
        $this->increment('stock', $quantity);
    }

    public function hasStock($quantity)
    {
        return $this->stock >= $quantity;
    }
}