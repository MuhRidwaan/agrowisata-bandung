<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'start_time',
        'end_time',
        'quota',
        'base_price',
        'thumbnail',
        'is_active'
    ];
    public function pricingTiers()
    {
        return $this->hasMany(PricingTier::class);
    }

    public function availableDates()
    {
        return $this->hasMany(AvailableDate::class);
    }


}
