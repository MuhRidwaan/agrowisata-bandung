<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'vendor_id',
        'phone_number',
        'message_template'
    ];

    // 🔥 RELASI KE VENDOR
    public function vendor()
    {
        return $this->belongsTo(\App\Models\Vendor::class);
    }
}