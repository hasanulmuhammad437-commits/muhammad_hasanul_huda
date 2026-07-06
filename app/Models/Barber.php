<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}