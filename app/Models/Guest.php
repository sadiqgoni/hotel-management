<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'preferences',
        'nin_number',
        'bonus_code'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function laundries()
    {
        return $this->hasMany(Laundry::class);
    }

    public function restaurantOrders()
    {
        return $this->hasMany(RestaurantOrder::class);
    }
}
