<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanggalAvailable extends Model
{
    use HasFactory;
    protected $table = 'tanggal_available';
    protected $fillable = [
        'paket_tour_id',
        'tanggal',
        'kuota',
        'status',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class, 'paket_tour_id');
    }
}
