<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'jam_operasional',
        'harga_paket',
        'kuota',
        'vendor_id',
    ];

    public function photos()
    {
        return $this->hasMany(PaketTourPhoto::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(PricingTier::class);
    }

    public function tanggalAvailables()
    {
        return $this->hasMany(TanggalAvailable::class, 'paket_tour_id');
    }

        public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
