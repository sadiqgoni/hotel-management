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
        'bonus_code',
        'stay_count',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function groupReservations()
    {
        return $this->hasMany(GroupReservation::class, 'primary_guest_id');
    }
    public function reservationWaitlist()
    {
        return $this->hasMany(ReservationWaitlist::class);
    }

    public function checkIn()
    {
        return $this->hasMany(CheckIn::class);
    }

    public function carRental()
    {
        return $this->hasMany(CarRental::class);
    }

}
