<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Area;
use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    // ================= LIST =================
    public function index()
    {
        $vendors = Vendor::with('area')->paginate(10);
        return view('.backend.vendors.index', compact('vendors'));
    }

    // ================= FORM CREATE =================
    public function create()
    {
        $areas = Area::all();
        return view('.backend.vendors.form', compact('areas'));
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'area_id' => 'required|exists:areas,id',
            'address' => 'required',
            'description' => 'required',
        ]);

        Vendor::create($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil ditambahkan');
    }

    // ================= FORM EDIT =================
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        $areas = Area::all();

        return view('.backend.vendors.form', compact('vendor', 'areas'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'area_id' => 'required|exists:areas,id',
            'address' => 'required',
            'description' => 'required',
        ]);

        $vendor = Vendor::findOrFail($id);
        $vendor->update($validated);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil diupdate');
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        Vendor::destroy($id);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor berhasil dihapus');
    }

    // ================= DETAIL =================
    public function show($id)
    {
        $vendor = Vendor::with('reviews')->findOrFail($id);
        $avgRating = $vendor->reviews()->avg('rating');

        return view('.backend.vendors.show', compact('vendor', 'avgRating'));
    }

    // ================= WHATSAPP =================
    public function contact($id)
    {
        $vendor = Vendor::findOrFail($id);

        $wa = WhatsappSetting::where('is_active', 1)->first();

        if (!$wa) {
            return redirect()->back()->with('error', 'WhatsApp setting belum diatur');
        }

        $message = str_replace('{{nama}}', $vendor->name, $wa->message_template);

        $link = "https://wa.me/" . $wa->phone_number . "?text=" . urlencode($message);

        return redirect($link);
    }
}
