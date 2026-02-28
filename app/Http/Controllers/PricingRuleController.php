<?php

namespace App\Http\Controllers;

use App\Models\PricingRule;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    public function index()
    {
        $rules = PricingRule::with('paketTour')->latest()->get();
        return view('backend.pricingrules.index', compact('rules'));
    }

    public function create()
    {
        $packages = PaketTour::pluck('nama_paket','id');
        return view('backend.pricingrules.form', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'rules' => 'required|array|min:1',
            'rules.*.min_pax' => 'required|integer|min:1',
            'rules.*.max_pax' => 'required|integer|gte:rules.*.min_pax',
            'rules.*.discount_type' => 'required|in:percent,nominal',
            'rules.*.discount_value' => 'required|integer|min:0',
            'rules.*.description' => 'nullable|string|max:255',
        ]);

        // Validasi overlap antar rules yang diinputkan & duplikat persis
        $inputRules = $validated['rules'];
        foreach ($inputRules as $i => $rule) {
            foreach ($inputRules as $j => $other) {
                if ($i !== $j) {
                    // Cek overlap range
                    if ($rule['min_pax'] <= $other['max_pax'] && $rule['max_pax'] >= $other['min_pax']) {
                        return back()->withErrors(['rules.'.$i.'.min_pax' => 'Range pax bertabrakan antar input rules.'])->withInput();
                    }
                    // Cek duplikat persis
                    if (
                        $rule['min_pax'] == $other['min_pax'] &&
                        $rule['max_pax'] == $other['max_pax'] &&
                        $rule['discount_type'] == $other['discount_type'] &&
                        $rule['discount_value'] == $other['discount_value'] &&
                        ($rule['description'] ?? '') == ($other['description'] ?? '')
                    ) {
                        return back()->withErrors(['rules.'.$i.'.min_pax' => 'Ada input rule yang persis sama, mohon cek kembali.'])->withInput();
                    }
                }
            }
        }
        // Validasi overlap dengan rules di DB
        foreach ($inputRules as $i => $rule) {
            $overlap = PricingRule::where('paket_tour_id', $validated['paket_tour_id'])
                ->where(function($q) use ($rule) {
                    $q->where('min_pax', '<=', $rule['max_pax'])
                      ->where('max_pax', '>=', $rule['min_pax']);
                })
                ->exists();
            if ($overlap) {
                return back()->withErrors(['rules.'.$i.'.min_pax' => 'Range pax bertabrakan dengan rule lain pada paket yang sama.'])->withInput();
            }
        }
        // Simpan semua rules
        foreach ($inputRules as $rule) {
            PricingRule::create([
                'paket_tour_id' => $validated['paket_tour_id'],
                'min_pax' => $rule['min_pax'],
                'max_pax' => $rule['max_pax'],
                'discount_type' => $rule['discount_type'],
                'discount_value' => $rule['discount_value'],
                'description' => $rule['description'] ?? null,
            ]);
        }
        return redirect()->route('pricingrules.index')
            ->with('success','Semua rule diskon berhasil ditambahkan');
    }

    public function edit(PricingRule $pricingrule)
    {
        $packages = PaketTour::pluck('nama_paket','id');
        // Ambil semua rules untuk paket ini
        $rules = PricingRule::where('paket_tour_id', $pricingrule->paket_tour_id)->orderBy('min_pax')->get()->map(function($r) {
            return [
                'min_pax' => $r->min_pax,
                'max_pax' => $r->max_pax,
                'discount_type' => $r->discount_type,
                'discount_value' => $r->discount_value,
                'description' => $r->description,
            ];
        })->toArray();
        return view('backend.pricingrules.form', [
            'rule' => $pricingrule,
            'rules' => $rules,
            'packages' => $packages
        ]);
    }

    public function update(Request $request, PricingRule $pricingrule)
    {
        $validated = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'rules' => 'required|array|min:1',
            'rules.*.min_pax' => 'required|integer|min:1',
            'rules.*.max_pax' => 'required|integer|gte:rules.*.min_pax',
            'rules.*.discount_type' => 'required|in:percent,nominal',
            'rules.*.discount_value' => 'required|integer|min:0',
            'rules.*.description' => 'nullable|string|max:255',
        ]);

        $inputRules = $validated['rules'];
        // Validasi overlap antar rules input & duplikat persis
        foreach ($inputRules as $i => $rule) {
            foreach ($inputRules as $j => $other) {
                if ($i !== $j) {
                    // Cek overlap range
                    if ($rule['min_pax'] <= $other['max_pax'] && $rule['max_pax'] >= $other['min_pax']) {
                        return back()->withErrors(['rules.'.$i.'.min_pax' => 'Range pax bertabrakan antar input rules.'])->withInput();
                    }
                    // Cek duplikat persis
                    if (
                        $rule['min_pax'] == $other['min_pax'] &&
                        $rule['max_pax'] == $other['max_pax'] &&
                        $rule['discount_type'] == $other['discount_type'] &&
                        $rule['discount_value'] == $other['discount_value'] &&
                        ($rule['description'] ?? '') == ($other['description'] ?? '')
                    ) {
                        return back()->withErrors(['rules.'.$i.'.min_pax' => 'Ada input rule yang persis sama, mohon cek kembali.'])->withInput();
                    }
                }
            }
        }
        // Hapus semua rules lama untuk paket ini, lalu insert ulang
        PricingRule::where('paket_tour_id', $validated['paket_tour_id'])->delete();
        foreach ($inputRules as $rule) {
            PricingRule::create([
                'paket_tour_id' => $validated['paket_tour_id'],
                'min_pax' => $rule['min_pax'],
                'max_pax' => $rule['max_pax'],
                'discount_type' => $rule['discount_type'],
                'discount_value' => $rule['discount_value'],
                'description' => $rule['description'] ?? null,
            ]);
        }
        return redirect()->route('pricingrules.index')
            ->with('success','Semua rule diskon berhasil diperbarui');
    }

    public function destroy(PricingRule $pricingrule)
    {
        $pricingrule->delete();
        return back()->with('success','Rule dihapus');
    }
}
