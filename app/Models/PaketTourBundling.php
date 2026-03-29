<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTourBundling extends Model
{
    use HasFactory;

    protected $fillable = [
        'paket_tour_id',
        'label',
        'people_count',
        'bundle_price',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'bundle_price' => 'decimal:2',
        'people_count' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }

    public function photos()
    {
        return $this->hasMany(PaketTourBundlingPhoto::class, 'paket_tour_bundling_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
