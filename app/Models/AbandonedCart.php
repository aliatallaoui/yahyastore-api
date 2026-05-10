<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    protected $fillable = ['session_id', 'phone', 'items', 'total'];

    protected $casts = [
        'items' => 'array',
    ];
}
