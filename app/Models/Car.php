<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $fillable = [
        'car_name', 
        'car_type', 
        'number_plate', 
        'availability_status', 
        'rate_per_hour',
    ];

    // Car has many rentals
    public function carRental()
    {
        return $this->hasMany(CarRental::class);
    }
}
