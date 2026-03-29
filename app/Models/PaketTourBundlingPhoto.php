<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourBundlingPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'paket_tour_bundling_id',
        'path_foto',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function bundling()
    {
        return $this->belongsTo(PaketTourBundling::class, 'paket_tour_bundling_id');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return storage_asset_url($this->path_foto);
    }
}
