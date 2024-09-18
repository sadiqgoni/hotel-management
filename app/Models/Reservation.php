<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    
    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'number_of_people',
        'price_per_night',
        'total_amount',
        'amount_paid',
        'payment_method',
        'coupon_management_id',
        'coupon_discount',
        'status',
        'payment_status',
        'special_requests',
    ];


    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
    public function checkInCheckOuts()
    {
        return $this->hasMany(CheckInCheckOut::class);
    }
    public function couponManagement()
    {
        return $this->belongsTo(CouponManagement::class);
    }
}
