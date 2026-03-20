<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmProductPhoto extends Model
{
    protected $fillable = [
        'umkm_product_id',
        'path_foto',
    ];

    public function umkmProduct()
    {
        return $this->belongsTo(UmkmProduct::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->path_foto ? asset('storage/' . $this->path_foto) : null;
    }
}
