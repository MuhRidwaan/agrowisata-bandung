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
}