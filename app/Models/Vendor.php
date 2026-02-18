<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name','email','phone','address','description','area_id','status'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
