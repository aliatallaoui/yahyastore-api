<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_order', 'max_uses', 'used_count', 'active', 'expires_at'];

    protected $casts = ['active' => 'boolean', 'expires_at' => 'datetime'];

    public function isValid(int $orderTotal = 0): bool
    {
        if (!$this->active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($this->min_order && $orderTotal < $this->min_order) return false;
        return true;
    }

    public function discountAmount(int $subtotal): int
    {
        if ($this->type === 'percent') {
            return (int) min(round($subtotal * $this->value / 100), $subtotal);
        }
        return (int) min($this->value, $subtotal);
    }

    public function incrementUsed(): void
    {
        $this->increment('used_count');
    }
}
