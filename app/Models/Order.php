<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['guest_id', 'total_amount', 'payment_status'];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
