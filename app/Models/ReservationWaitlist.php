<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationWaitlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'waitlist_option',
        'room_type_id',
        'room_id',
        'desired_check_in_date',
        'desired_check_out_date',
        'is_notified',
    ];

    // Relationship to the Guest model
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Relationship to the RoomType model
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Relationship to the Room model
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
