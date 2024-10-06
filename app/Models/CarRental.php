<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'guest_id',
        'rented_at',
        'returned_at',
        'total_cost',
    ];

    // Rental belongs to a car
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    // Rental belongs to a guest
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
