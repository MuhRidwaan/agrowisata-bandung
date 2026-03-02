<?php

namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PricingTierController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = PricingTier::with('paketTour');

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            $query->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

        $tiers = $query->latest()->get();

        return view('backend.pricingtiers.index', compact('tiers'));
    }

    public function create()
    {
        $user = auth()->user();
        $query = PaketTour::orderBy('nama_paket');

        if ($user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
        }

        $packages = $query->pluck('nama_paket','id');

        return view('backend.pricingtiers.form', compact('packages'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'paket_tour_id' => [
                'required',
                'exists:paket_tours,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->hasRole('Vendor')) {
                        $exists = PaketTour::where('id', $value)
                            ->where('vendor_id', $user->vendor->id ?? null)
                            ->exists();
                        if (!$exists) {
                            $fail('Paket tour yang dipilih tidak valid.');
                        }
                    }
                },
            ],
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
        $user = auth()->user();
        $query = PaketTour::orderBy('nama_paket');

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
            // Pastikan data milik vendor yang login
            if ($pricingtier->paketTour->vendor_id !== $vendorId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $packages = $query->pluck('nama_paket','id');

        return view('backend.pricingtiers.form', [
            'tier'     => $pricingtier,
            'packages' => $packages
        ]);
    }

    public function update(Request $request, PricingTier $pricingtier)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'paket_tour_id' => [
                'required',
                'exists:paket_tours,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->hasRole('Vendor')) {
                        $exists = PaketTour::where('id', $value)
                            ->where('vendor_id', $user->vendor->id ?? null)
                            ->exists();
                        if (!$exists) {
                            $fail('Paket tour yang dipilih tidak valid.');
                        }
                    }
                },
            ],
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