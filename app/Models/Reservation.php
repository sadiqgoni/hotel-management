<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'guest_id', 'room_id', 'check_in_date', 'check_out_date', 
        'price_per_night', 'total_amount', 'status', 'special_requests', 
        'number_of_people'
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
}
