<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'guest_id',
        'room_id',
        'check_in_date',
        'check_out_date',
        'total_amount',
        'price_per_night',
        'special_requests',
        'number_of_people',
        'status',
    ];

    // Define the relationship with the Guest model
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    // Define the relationship with the Room model
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    // Define a hasMany relationship to CheckInCheckOut
    public function checkInCheckOuts()
    {
        return $this->hasMany(CheckInCheckOut::class);
    }
}
