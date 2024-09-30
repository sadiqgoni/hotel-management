<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',      
        'guest_id',       
        'table_id',      
        'service_charge',
        'total_amount',
        'status',    
        'amount_paid',   
        'customer_type', 
        'change_amount' ,
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
