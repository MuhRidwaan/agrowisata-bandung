<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'user_id','name','email','phone','address','description','area_id','status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function paketTours()
    {
        return $this->hasMany(PaketTour::class);
    }

    public function umkmProducts()
    {
        return $this->hasMany(UmkmProduct::class);
    }

    public function whatsappsetting()
    {
        return $this->hasOne(WhatsappSetting::class, 'vendor_id');
    }
}
