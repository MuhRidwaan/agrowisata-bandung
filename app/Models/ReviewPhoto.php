<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ReviewPhoto extends Model
{
    protected $fillable = [
        'review_id',
        'photo'
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        $path = ltrim(str_replace('\\', '/', $this->photo), '/');

        if (str_starts_with($path, 'uploads/')) {
            return '/' . $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }

        return '/storage/' . $path;
    }
}
