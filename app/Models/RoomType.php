<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $table = 'room_types';

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'max_occupancy',

    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
    public function reservationWaitlist()
    {
        return $this->hasMany(ReservationWaitlist::class);
    }
}
