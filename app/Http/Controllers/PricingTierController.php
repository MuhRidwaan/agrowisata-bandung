<?php

namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Models\TourPackage;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PricingTierController extends Controller
{
    /**
     * List semua pricing tier
     */
    public function index()
    {
        $tiers = PricingTier::with('tourPackage')
                    ->latest()
                    ->get();

        return view('backend.pricingtiers.index', compact('tiers'));
    }

    /**
     * Form tambah
     */
    public function create()
    {
        $packages = PaketTour::orderBy('nama_paket')
                ->pluck('nama_paket','id');

        return view('backend.pricingtiers.form', compact('packages'));
    }

    /**
     * Simpan data baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'name'            => 'required|string|max:100',
            'price'           => 'required|integer|min:0',
        ]);

        PricingTier::create($validated);

        return redirect()
            ->route('pricingtiers.index')
            ->with('success', 'Kategori harga berhasil ditambahkan');
    }

    /**
     * Form edit
     */
    public function edit(PricingTier $pricingTier)
    {
        $packages = TourPackage::orderBy('title')
                        ->pluck('title','id');

        return view('backend.pricingtiers.form', [
            'tier' => $pricingTier,
            'packages' => $packages
        ]);
    }

    /**
     * Update data
     */
    public function update(Request $request, PricingTier $pricingTier)
    {
        $validated = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'name'            => 'required|string|max:100',
            'price'           => 'required|integer|min:0',
        ]);

        $pricingTier->update($validated);

        return redirect()
            ->route('pricingtiers.index')
            ->with('success', 'Kategori harga berhasil diperbarui');
    }

    /**
     * Hapus data
     */
    public function destroy(PricingTier $pricingTier)
    {
        $pricingTier->delete();

        return redirect()
            ->route('pricingtiers.index')
            ->with('success', 'Kategori harga berhasil dihapus');
    }
}
