<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'short_desc',
        'price', 'old_price', 'discount_percent',
        'category', 'category_label', 'image',
        'features', 'gallery_images', 'related_product_ids',
        'sort_order', 'active', 'stock',
    ];

    protected $casts = [
        'features' => 'array',
        'gallery_images' => 'array',
        'related_product_ids' => 'array',
        'active' => 'boolean',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function relatedProducts()
    {
        $ids = $this->related_product_ids ?? [];
        return static::whereIn('id', $ids)->where('active', true)->get();
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, '.', ',') . ' DZD';
    }

    public function getFormattedOldPriceAttribute(): ?string
    {
        return $this->old_price
            ? number_format($this->old_price, 0, '.', ',') . ' DZD'
            : null;
    }

    public function getSavingsAttribute(): ?string
    {
        if (!$this->old_price) return null;
        return number_format($this->old_price - $this->price, 0, '.', ',') . ' DZD';
    }
}
