<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'description',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the vendor that owns this product
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the paket tours that include this product
     */
    public function paketTours()
    {
        return $this->belongsToMany(
            PaketTour::class,
            'paket_tour_umkm',
            'umkm_product_id',
            'paket_tour_id'
        )->withTimestamps();
    }

    /**
     * Get the bookings that include this product
     */
    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'booking_umkm',
            'umkm_product_id',
            'booking_id'
        )->withPivot('quantity', 'price')
         ->withTimestamps();
    }

    /**
     * Get the product photos
     */
    public function photos()
    {
        return $this->hasMany(UmkmProductPhoto::class);
    }
    
    public function getPhotoUrlAttribute()
    {
        $photo = $this->photos->first();

        return $photo 
            ? asset('storage/' . $photo->photo) 
            : 'https://via.placeholder.com/60';
    }
}
