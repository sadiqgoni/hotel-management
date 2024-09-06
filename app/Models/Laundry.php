<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laundry extends Model
{
    use HasFactory;

    protected $table = 'laundries';

    protected $fillable = [
        'guest_id',
        'item',
        'status',
        'amount',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
