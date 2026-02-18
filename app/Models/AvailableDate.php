<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableDate extends Model
{
    protected $fillable = [
        'tour_package_id',
        'date',
        'quota',
        'booked'
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
