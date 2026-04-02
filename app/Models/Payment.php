<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'payment_method',
        'selected_channel',
        'transaction_id',
        'status',
        'paid_at',
        'invoice_emailed_at',
        'snap_token',
        'transfer_proof',
        'transfer_proof_uploaded_at',
        'admin_note',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'invoice_emailed_at' => 'datetime',
        'transfer_proof_uploaded_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
