<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class GroupReservation extends Model
{
    use HasFactory;

 
    // Define the fillable fields to protect against mass assignment
    protected $fillable = [
        'organization_name',
        'contact_person',
        'contact_phone',
        'contact_email',
        'group_size',
        'primary_guest_id',
        'coupon_management_id',
        'total_amount',
        'amount_paid',
        'remaining_balance',
        'room_ids',           // Storing room IDs as a JSON array
        'check_in_date',
        'check_out_date',
        'special_requests',
        'payment_method',
        'coupon_discount',
        'payment_status',
        'price_per_night',
        'status',
    ];

    // Cast the `room_ids` field as an array to easily work with it in Laravel
    protected $casts = [
        'room_ids' => 'array',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
    ];

    // Define relationships
    public function primaryGuest()
    {
        return $this->belongsTo(Guest::class, 'primary_guest_id');
    }

    public function coupon()
    {
        return $this->belongsTo(CouponManagement::class,);
    }

    // Example of how you might get rooms from the room_ids array
    public function rooms()
    {
        return Room::whereIn('id', $this->room_ids)->get();
    }
}
