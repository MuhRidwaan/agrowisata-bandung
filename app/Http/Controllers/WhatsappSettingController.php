<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use App\Models\Vendor;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function index()
    {
        $settings = WhatsappSetting::with('vendor')->get();
        return view('backend.whatsappsetting.index', compact('settings'));
    }

    public function create()
    {
        $vendors = Vendor::orderBy('name')->get();

        //  TEMPLATE SIAP PAKAI
        $templates = [
            'Halo {nama_vendor}, saya tertarik dengan layanan yang Anda tawarkan. Mohon informasi lebih lanjut ya ',
            'Halo {nama_vendor}, saya ingin menanyakan detail paket atau layanan yang tersedia. Terima kasih ',
            'Halo {nama_vendor}, saya ingin melakukan booking. Mohon info ketersediaannya ',
            'Halo {nama_vendor}, saya ingin mengetahui informasi harga layanan yang tersedia ',
        ];

        return view('backend.whatsappsetting.form', compact('vendors', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'phone_number' => 'required',
            'message_template' => 'required',
        ]);

        WhatsappSetting::create($validated);

        return redirect()->route('whatsappsetting.index')
            ->with('success', 'Data berhasil disimpan');
    }

    public function edit($id)
    {
        $setting = WhatsappSetting::findOrFail($id);
        $vendors = Vendor::orderBy('name')->get();

        // TEMPLATE JUGA HARUS ADA DI EDIT
        $templates = [
            'Halo {nama_vendor}, saya tertarik dengan layanan yang Anda tawarkan. Mohon informasi lebih lanjut ya ',
            'Halo {nama_vendor}, saya ingin menanyakan detail paket atau layanan yang tersedia. Terima kasih ',
            'Halo {nama_vendor}, saya ingin melakukan booking. Mohon info ketersediaannya ',
            'Halo {nama_vendor}, saya ingin mengetahui informasi harga layanan yang tersedia ',
        ];

        return view('backend.whatsappsetting.form', compact('setting', 'vendors', 'templates'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'phone_number' => 'required',
            'message_template' => 'required',
        ]);

        $setting = WhatsappSetting::findOrFail($id);
        $setting->update($validated);

        return redirect()->route('whatsappsetting.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        WhatsappSetting::destroy($id);

        return redirect()->route('whatsappsetting.index')
            ->with('success', 'Data berhasil dihapus');
    }
}