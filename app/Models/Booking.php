<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const QUOTA_RESERVED_STATUSES = [
        'pending',
        'confirmed',
        'paid',
    ];

    protected $fillable = [
        'booking_code',
        'user_id',
        'paket_tour_id',
        'jumlah_peserta',
        'total_price',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'visit_date',
        'tanggal',
    ];

    public function scopeReservingQuota($query)
    {
        return $query->whereIn('status', self::QUOTA_RESERVED_STATUSES);
    }

    public static function reservedParticipantsForDate(int $paketTourId, string|CarbonInterface $visitDate, ?int $ignoreBookingId = null): int
    {
        $query = static::query()
            ->where('paket_tour_id', $paketTourId)
            ->reservingQuota()
            ->whereDate('visit_date', $visitDate);

        if ($ignoreBookingId) {
            $query->whereKeyNot($ignoreBookingId);
        }

        return (int) $query->sum('jumlah_peserta');
    }

    public function paketTour()
    {
        return $this->belongsTo(PaketTour::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function umkmProducts()
    {
        return $this->belongsToMany(
            UmkmProduct::class,
            'booking_umkm',
            'booking_id',
            'umkm_product_id'
        )->withPivot('quantity', 'price')->withTimestamps();
    }
}
