<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',        // The user (restaurant staff) who placed the order
        'guest_id',       // Optional if linked to a hotel guest
        'table_id',       // Optional if linked to a specific table
        'subtotal',
        'tax',
        'total_amount',
        'status',         // Order status: 'pending', 'completed', 'canceled'
        'customer_type',  
        'dining_option',
        'billing_option',
        'payment_method',
    ];
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
