<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Review extends Model
{
    protected $fillable = [
        'paket_id',
        'user_id',
        'vendor_id',
        'name',
        'rating',
        'comment',
        'photo',
        'status',
        'admin_reply'
    ];

    protected $attributes = [
        'status' => 'pending'
    ];

    // ================= RELATION =================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function paket()
    {
        return $this->belongsTo(PaketTour::class,'paket_id');
    }

    public function photos()
    {
        return $this->hasMany(\App\Models\ReviewPhoto::class);
    }

    // ================= HELPER =================

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning',
        };
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return storage_asset_url($this->photo);
    }
}
