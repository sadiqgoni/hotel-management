<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CouponManagement extends Model
{
    use HasFactory;

    protected $table = 'coupon_managements';

    // Mass-assignable attributes
    protected $fillable = [
        'code',
        'description',
        'discount_type', 
        'discount_amount',
        'discount_percentage',
        'valid_from',
        'valid_until',
        'usage_limit',
        'times_used',
        'status',
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }
    // Accessor for checking if a coupon is valid
    public function getIsValidAttribute()
    {
        $now = Carbon::now();
        return $this->status === 'active' && 
               $now->between($this->valid_from, $this->valid_until) &&
               $this->times_used < $this->usage_limit;
    }

    // Scope for active coupons
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('valid_until', '>=', Carbon::now());
    }
}

