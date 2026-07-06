<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['user_id', 'service_id', 'barber_id', 'booking_date', 'booking_time', 'total_price', 'payment_status', 'snap_token'];

    public function user() { return $this->belongsTo(User::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function barber() { return $this->belongsTo(Barber::class); }
}