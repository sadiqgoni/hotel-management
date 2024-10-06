<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'reservation_number',
        'check_in_time',
        'check_out_time',
        'room_number',  
        'guest_name',   
        'guest_phone',  
        'paid_amount',  
        'due_amount',  
        'booking_status',
        'payment_status',
        'coupon_management',
        'coupon_discount',
        'price_per_night',
        'frequent_guest_message',
        'number_of_nights',
        'special_requests',
        'number_of_people',
        'total_amount',
        'restaurant_bill',
    ];

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
