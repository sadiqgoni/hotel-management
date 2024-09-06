<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInCheckOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'check_in_time',
        'check_out_time',
        'status',
    ];

    // Relationships
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

 
   

    // Scopes
    public function scopeConfirmedReservations($query)
    {
        return $query->whereHas('reservation', function ($query) {
            $query->where('status', 'Confirmed');
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }
}
