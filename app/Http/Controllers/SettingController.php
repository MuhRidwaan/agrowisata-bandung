<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('category');
        return view('backend.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Get all settings keys that are checkboxes
        $checkboxKeys = Setting::where('type', 'checkbox')->pluck('key')->toArray();

        foreach (Setting::all() as $setting) {
            $key = $setting->key;
            
            if ($setting->type === 'file' && $request->hasFile($key)) {
                // Hapus file lama jika ada
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                $path = $request->file($key)->store('branding', 'public');
                $setting->update(['value' => $path]);
            } elseif (in_array($key, $checkboxKeys)) {
                // Handle checkbox: if present it's 'true', if not it's 'false'
                $value = $request->has($key) ? 'true' : 'false';
                $setting->update(['value' => $value]);
            } elseif ($request->has($key)) {
                $setting->update(['value' => $request->get($key)]);
            }
        }

        return back()->with('success', 'Global Settings updated successfully!');
    }
}
