<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProductPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'umkm_product_id',
        'path_foto',
    ];

    public function umkmProduct()
    {
        return $this->belongsTo(UmkmProduct::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        return storage_asset_url($this->path_foto, asset('frontend/img/logo.png'))
            ?? asset('frontend/img/logo.png');
    }
}
