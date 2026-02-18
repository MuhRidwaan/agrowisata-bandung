<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourGallery extends Model
{
    protected $fillable = [
        'tour_package_id',
        'image',
        'caption'
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function galleries()
    {
    return $this->hasMany(TourGallery::class);
    }

}
