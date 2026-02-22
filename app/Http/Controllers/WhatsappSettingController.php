<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function index()
    {
        $settings = WhatsappSetting::all();
        return view('backend.whatsappsetting.index', compact('settings'));
    }

    public function create()
    {
        return view('backend.whatsappsetting.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^\+?62[0-9]+$/',
            'message_template' => 'required',
            'is_active' => 'required|in:0,1',
    ]);

        WhatsappSetting::create($validated);

        return redirect()->route('whatsappsetting.index')
            ->with('success', 'Data berhasil disimpan');
    }


    public function edit($id)
    {
        $setting = WhatsappSetting::findOrFail($id);
        return view('backend.whatsappsetting.form', compact('setting'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'phone_number' => 'required|regex:/^62[0-9]+$/',
            'message_template' => 'required',
            'is_active' => 'required|in:0,1',
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
