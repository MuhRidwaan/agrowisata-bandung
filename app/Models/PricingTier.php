<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    protected $fillable = [
        'paket_tour_id',
        'name',
        'price',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }

    public function pricingTiers()
    {
    return $this->hasMany(PricingTier::class, 'paket_tour_id');
    }
}