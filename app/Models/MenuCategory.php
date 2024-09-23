<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}
