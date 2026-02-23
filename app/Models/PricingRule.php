<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    protected $fillable = [
        'paket_tour_id',
        'min_pax',
        'max_pax',
        'discount_type',
        'discount_value',
        'description',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class, 'paket_tour_id');
    }
}
