<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends Model
{
    protected $guarded = [];

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
