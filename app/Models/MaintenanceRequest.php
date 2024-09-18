<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRequest extends Model
{
   use HasFactory;
   protected $table = 'maintenance_requests';


    protected $fillable = [
        'room_number',
        'maintenance_details',
        'status',
        'created_by',
        'updated_by',
        'user_id',

    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
