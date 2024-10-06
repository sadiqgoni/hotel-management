<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'room_number',
        'room_type_id',
        'price_per_night',
        'status',
        'is_clean',
        'note',
        'max_occupancy',
        'description',
        'housekeeper_id',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function housekeeper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'housekeeper_id');
    }
    public function groupReservation()
    {
        return $this->hasMany(GroupReservation::class);
    }
    public function reservationWaitlist()
    {
        return $this->hasMany(ReservationWaitlist::class);
    }
    public function checkIn()
    {
        return $this->hasMany(CheckIn::class);
    }
}
