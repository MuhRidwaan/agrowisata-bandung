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
        'jam_awal',
        'jam_akhir',
        'harga_paket',
        'kuota',
        'vendor_id',
    ];

    /**
     * Casting kolom TIME agar menjadi instance Carbon
     */
    protected $casts = [
        'jam_awal'  => 'datetime:H:i:s',
        'jam_akhir' => 'datetime:H:i:s',
    ];

    /**
     * Accessor untuk format tampilan jam operasional
     */
    public function getJamOperasionalAttribute()
    {
        if (!$this->jam_awal || !$this->jam_akhir) {
            return '-';
        }

        return $this->jam_awal->format('H:i') . ' to ' . $this->jam_akhir->format('H:i');
    }

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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'vendor_id', 'vendor_id');
    }
}