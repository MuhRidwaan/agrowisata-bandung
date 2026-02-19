<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'user_id',
        'paket_tour_id',
        'jumlah_peserta',
        'total_price',
        'status',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
