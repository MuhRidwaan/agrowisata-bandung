<?php

namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PricingTierController extends Controller
{
    public function index()
    {
        $tiers = PricingTier::with('paketTour')->get();
        return view('pricing_tier.index', compact('tiers'));
    }

    public function create()
    {
        $paketTours = PaketTour::all();
        return view('pricing_tier.form', ['tier' => new PricingTier(), 'paketTours' => $paketTours]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'qty_min' => 'required|integer',
            'qty_max' => 'required|integer',
            'harga' => 'required|numeric',
        ]);
        PricingTier::create($data);
        return redirect()->route('pricing-tiers.index')->with('success', 'Pricing Tier berhasil ditambahkan!');
    }

    public function edit(PricingTier $pricingTier)
    {
        $paketTours = PaketTour::all();
        return view('pricing_tier.form', ['tier' => $pricingTier, 'paketTours' => $paketTours]);
    }

    public function update(Request $request, PricingTier $pricingTier)
    {
        $data = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'qty_min' => 'required|integer',
            'qty_max' => 'required|integer',
            'harga' => 'required|numeric',
        ]);
        $pricingTier->update($data);
        return redirect()->route('pricing-tiers.index')->with('success', 'Pricing Tier berhasil diupdate!');
    }

    public function destroy(PricingTier $pricingTier)
    {
        $pricingTier->delete();
        return redirect()->route('pricing-tiers.index')->with('success', 'Pricing Tier berhasil dihapus!');
    }
}
