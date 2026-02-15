<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'paket_tour_id',
        'path_foto',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }
}
