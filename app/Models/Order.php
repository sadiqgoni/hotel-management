<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id', 
        'invoice_number',     
        'guest_info',       
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
        'room_number'
    ];
  

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
