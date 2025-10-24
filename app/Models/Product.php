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
        'category_id',
        'image',
        'gallery',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // 🔧 TAMBAHKAN SCOPE ACTIVE INI
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive()
    {
        return $this->is_active;
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

    // 🔧 TAMBAHKAN METHOD UNTUK RELASI CATEGORY (jika sudah ada migration category)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 🔧 TAMBAHKAN METHOD UNTUK IMAGES (jika sudah ada migration images)
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return asset('images/default-product.jpg');
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        
        return asset('storage/' . $this->image);
    }

    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery) {
            return [asset('images/default-product.jpg')];
        }
        
        $gallery = is_string($this->gallery) ? json_decode($this->gallery, true) : $this->gallery;
        
        return collect($gallery)->map(function ($image) {
            if (str_starts_with($image, 'http')) {
                return $image;
            }
            return asset('storage/' . $image);
        })->toArray();
    }
}
