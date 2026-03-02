<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTour extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'jam_awal',
        'jam_akhir',
        'harga_paket',
        'kuota',
        'aktivitas',
        'vendor_id',
    ];

    /**
     * Casting kolom TIME agar menjadi instance Carbon
     */
    protected $casts = [
        'jam_awal'  => 'datetime:H:i:s',
        'jam_akhir' => 'datetime:H:i:s',
        'aktivitas' => 'array',
    ];

    /**
     * Accessor untuk format tampilan jam operasional
     */
    public function getJamOperasionalAttribute()
    {
        if (!$this->jam_awal || !$this->jam_akhir) {
            return '-';
        }

        return $this->jam_awal->format('H:i') . ' to ' . $this->jam_akhir->format('H:i');
    }

    public function photos()
    {
        return $this->hasMany(PaketTourPhoto::class);
    }

    public function pricingTiers()
    {
        return $this->hasMany(PricingTier::class);
    }

    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class, 'paket_tour_id');
    }

    /**
     * Menghitung total harga berdasarkan jumlah peserta dan aturan diskon (Pricing Rules)
     * 
     * @param int $pax Jumlah peserta
     * @return array Detail perhitungan [base_price, discount, total_price, applied_rule]
     */
    public function calculatePrice($pax)
    {
        $basePrice = $this->harga_paket;
        $totalBase = $basePrice * $pax;
        $discount = 0;
        $appliedRule = null;

        // Cari rule yang sesuai dengan jumlah peserta
        $rule = $this->pricingRules()
            ->where('min_pax', '<=', $pax)
            ->where(function($query) use ($pax) {
                $query->where('max_pax', '>=', $pax)
                      ->orWhereNull('max_pax');
            })
            ->first();

        if ($rule) {
            $appliedRule = $rule;
            if ($rule->discount_type === 'percent') {
                $discount = ($totalBase * $rule->discount_value) / 100;
            } elseif ($rule->discount_type === 'nominal') {
                $discount = $rule->discount_value;
            }
        }

        return [
            'base_price_per_pax' => $basePrice,
            'total_base_price' => $totalBase,
            'discount' => $discount,
            'total_price' => $totalBase - $discount,
            'applied_rule' => $appliedRule
        ];
    }

    public function tanggalAvailables()
    {
        return $this->hasMany(TanggalAvailable::class, 'paket_tour_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'vendor_id', 'vendor_id');
    }
}