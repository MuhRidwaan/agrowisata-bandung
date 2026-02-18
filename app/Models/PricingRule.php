<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    protected $fillable = [
        'tour_package_id',
        'min_pax',
        'max_pax',
        'discount_type',
        'discount_value',
        'description',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
