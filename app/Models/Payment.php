<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at',
        'snap_token',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
