<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'seats',
        'is_available',
    ];

    // You can add relationships here if needed, e.g. orders tied to a table
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
