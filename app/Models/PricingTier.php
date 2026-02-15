<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'paket_tour_id',
        'qty_min',
        'qty_max',
        'harga',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
