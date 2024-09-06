<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    use HasFactory;

    protected $table = 'restaurant_orders';

    protected $fillable = [
        'guest_id',
        'items',
        'total_amount',
        'status',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    protected $casts = [
        'items' => 'array',
    ];
}
