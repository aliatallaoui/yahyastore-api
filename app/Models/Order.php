<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'name', 'phone',
        'wilaya_code', 'wilaya_name', 'address', 'notes',
        'subtotal', 'shipping', 'total',
        'payment_method', 'status', 'whatsapp_url',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
