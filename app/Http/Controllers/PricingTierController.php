<?php

namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PricingTierController extends Controller
{
    public function index()
    {
        $tiers = PricingTier::with('paketTour')
                    ->latest()
                    ->get();

        return view('backend.pricingtiers.index', compact('tiers'));
    }

    public function create()
    {
        $packages = PaketTour::orderBy('nama_paket')
                    ->pluck('nama_paket','id');

        return view('backend.pricingtiers.form', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'name'          => 'required|string|max:100',
            'price'         => 'required|integer|min:0',
        ]);

        PricingTier::create($validated);

        return redirect()
            ->route('pricingtiers.index')
            ->with('success', 'Kategori harga berhasil ditambahkan');
    }

    public function edit(PricingTier $pricingtier)
    {
        $packages = PaketTour::orderBy('nama_paket')
                        ->pluck('nama_paket','id');

        return view('backend.pricingtiers.form', [
            'tier'     => $pricingtier,
            'packages' => $packages
        ]);
    }

    public function update(Request $request, PricingTier $pricingtier)
    {
        $validated = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'name'          => 'required|string|max:100',
            'price'         => 'required|integer|min:0',
        ]);

        $pricingtier->update($validated);

        return redirect()
            ->route('pricingtiers.index')
            ->with('success', 'Kategori harga berhasil diperbarui');
    }

    public function destroy(PricingTier $pricingtier)
    {
        $pricingtier->delete();

        return redirect()
            ->route('pricingtiers.index')
            ->with('success', 'Kategori harga berhasil dihapus');
    }
}