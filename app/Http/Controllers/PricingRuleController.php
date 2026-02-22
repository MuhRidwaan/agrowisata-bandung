<?php

namespace App\Http\Controllers;

use App\Models\PricingRule;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    public function index()
    {
        $rules = PricingRule::with('tourPackage')->latest()->get();
        return view('backend.pricingrules.index', compact('rules'));
    }

    public function create()
    {
        $packages = TourPackage::pluck('title','id');
        return view('backend.pricingrules.form', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'min_pax' => 'required|integer|min:1',
            'max_pax' => 'required|integer|gte:min_pax',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Validasi overlap range pax
        $overlap = PricingRule::where('tour_package_id', $data['tour_package_id'])
            ->where(function($q) use ($data) {
                $q->where('min_pax', '<=', $data['max_pax'])
                  ->where('max_pax', '>=', $data['min_pax']);
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['min_pax' => 'Range pax bertabrakan dengan rule lain pada paket yang sama.'])->withInput();
        }

        PricingRule::create($data);

        return redirect()->route('pricingrules.index')
            ->with('success','Rule diskon berhasil ditambahkan');
    }

    public function edit(PricingRule $pricingRule)
    {
        $packages = TourPackage::pluck('title','id');

        return view('backend.pricingrules.form', [
            'rule' => $pricingRule,
            'packages' => $packages
        ]);
    }

    public function update(Request $request, PricingRule $pricingRule)
    {
        $data = $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'min_pax' => 'required|integer|min:1',
            'max_pax' => 'required|integer|gte:min_pax',
            'discount_type' => 'required|in:percent,nominal',
            'discount_value' => 'required|integer|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        // Validasi overlap range pax, kecuali rule ini sendiri
        $overlap = PricingRule::where('tour_package_id', $data['tour_package_id'])
            ->where('id', '!=', $pricingRule->id)
            ->where(function($q) use ($data) {
                $q->where('min_pax', '<=', $data['max_pax'])
                  ->where('max_pax', '>=', $data['min_pax']);
            })
            ->exists();
        if ($overlap) {
            return back()->withErrors(['min_pax' => 'Range pax bertabrakan dengan rule lain pada paket yang sama.'])->withInput();
        }

        $pricingRule->update($data);

        return redirect()->route('pricingrules.index')
            ->with('success','Rule diskon diperbarui');
    }

    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();
        return back()->with('success','Rule dihapus');
    }
}
