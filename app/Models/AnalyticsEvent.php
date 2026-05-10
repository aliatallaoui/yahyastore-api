<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'event', 'session_id', 'page', 'product_id', 'product_name', 'ip', 'ua',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
