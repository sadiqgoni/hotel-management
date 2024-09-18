<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffManagement extends Model
{
    use HasFactory;

    // Table associated with the model
    protected $table = 'staff_managements';

    // The attributes that are mass assignable
    protected $fillable = [
        'full_name',
        'email',
        'phone_number',
        'role',
        'status',
        'employment_date',
        'termination_date',
        'profile_picture',
        'address',
        'date_of_birth',
        'shift',
        'next_of_kin_name',
        'next_of_kin_address',
        'next_of_kin_phone_number'
    ];

    protected $casts = [
        'employment_date' => 'date',
        'termination_date' => 'date',
    ];
   
}
