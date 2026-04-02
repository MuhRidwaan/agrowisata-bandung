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
        'is_bundling_available',
        'harga_bundling',
        'bundling_people',
        'has_minimum_person',
        'minimum_person',
        'kuota',
        'aktivitas',
        'facilities',
        'vendor_id',
    ];

    /**
     * Casting kolom TIME agar menjadi instance Carbon
     */
    protected $casts = [
        'jam_awal'  => 'datetime:H:i:s',
        'jam_akhir' => 'datetime:H:i:s',
        'aktivitas' => 'array',
        'facilities' => 'array',
        'is_bundling_available' => 'boolean',
        'bundling_people' => 'integer',
        'has_minimum_person' => 'boolean',
        'minimum_person' => 'integer',
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

    public function bundlings()
    {
        return $this->hasMany(PaketTourBundling::class)
            ->orderBy('sort_order')
            ->orderBy('people_count');
    }

    /**
     * Menghitung total harga berdasarkan jumlah peserta dan aturan diskon (Pricing Rules)
     * 
     * @param int $pax Jumlah peserta
     * @return array Detail perhitungan [base_price, discount, total_price, applied_rule]
     */
    public function calculatePrice($pax, $useBundling = false, $bundlingId = null)
    {
        $selectedBundling = null;

        if ($useBundling) {
            $bundlings = $this->relationLoaded('bundlings')
                ? $this->bundlings->where('is_active', true)->values()
                : $this->bundlings()->where('is_active', true)->get();

            if ($bundlingId) {
                $selectedBundling = $bundlings->firstWhere('id', (int) $bundlingId);
            }

            if (! $selectedBundling) {
                $selectedBundling = $bundlings->firstWhere('people_count', (int) $pax);
            }
        }

        if ($selectedBundling && (int) $pax === (int) $selectedBundling->people_count) {
            return [
                'base_price_per_pax' => $this->harga_paket,
                'total_base_price' => $selectedBundling->bundle_price,
                'discount' => 0,
                'total_price' => $selectedBundling->bundle_price,
                'applied_rule' => null,
                'applied_bundling' => true,
                'bundling' => $selectedBundling,
            ];
        }

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
            'applied_rule' => $appliedRule,
            'applied_bundling' => false,
            'bundling' => null,
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
        return $this->hasMany(Review::class, 'paket_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function umkmProducts()
    {
        return $this->belongsToMany(UmkmProduct::class, 'paket_tour_umkm');
    }
}
