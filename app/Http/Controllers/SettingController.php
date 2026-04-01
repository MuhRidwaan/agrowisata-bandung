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

            if ($setting->type === 'json_channels') {
                // Handle dynamic payment channels
                $names         = $request->input("channels_name", []);
                $types         = $request->input("channels_type", []);
                $accountNums   = $request->input("channels_account_number", []);
                $accountNames  = $request->input("channels_account_name", []);
                $instructions  = $request->input("channels_instructions", []);
                $actives       = $request->input("channels_is_active", []);

                $channels = [];
                foreach ($names as $i => $name) {
                    if (blank($name)) continue;
                    $channels[] = [
                        'name'           => $name,
                        'type'           => $types[$i] ?? 'bank_transfer',
                        'account_number' => $accountNums[$i] ?? '',
                        'account_name'   => $accountNames[$i] ?? '',
                        'instructions'   => $instructions[$i] ?? '',
                        'is_active'      => isset($actives[$i]) && $actives[$i] === 'true',
                    ];
                }

                $setting->update(['value' => json_encode($channels)]);

            } elseif ($setting->type === 'file' && $request->hasFile($key)) {
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                $path = $request->file($key)->store('branding', 'public');
                $setting->update(['value' => $path]);
            } elseif (in_array($key, $checkboxKeys)) {
                $value = $request->has($key) ? 'true' : 'false';
                $setting->update(['value' => $value]);
            } elseif ($request->has($key)) {
                $setting->update(['value' => $request->get($key)]);
            }
        }

        return back()->with('success', 'Global Settings updated successfully!');
    }

    public function deleteLogo($id)
    {
        $setting = Setting::findOrFail($id);
            // Hapus file dari storage
            if ($setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }
            // Kosongkan value di database
            $setting->value = null;
            $setting->save();

        return back()->with('success', 'Logo berhasil dihapus!');
    }

    public function uploadChannelQr(Request $request, $index)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $setting = Setting::where('key', 'manual_payment_channels')->firstOrFail();
        $channels = json_decode($setting->value ?? '[]', true) ?? [];

        if (! isset($channels[$index])) {
            return back()->with('error', 'Channel tidak ditemukan.');
        }

        // Hapus QR lama jika ada
        if (! empty($channels[$index]['qr_image'])) {
            Storage::disk('public')->delete($channels[$index]['qr_image']);
        }

        $path = $request->file('qr_image')->store('payment_qr', 'public');
        $channels[$index]['qr_image'] = $path;

        $setting->update(['value' => json_encode($channels)]);

        return back()->with('success', 'QR Code berhasil diupload.');
    }

    public function deleteChannelQr($index)
    {
        $setting = Setting::where('key', 'manual_payment_channels')->firstOrFail();
        $channels = json_decode($setting->value ?? '[]', true) ?? [];

        if (isset($channels[$index]['qr_image'])) {
            Storage::disk('public')->delete($channels[$index]['qr_image']);
            $channels[$index]['qr_image'] = null;
            $setting->update(['value' => json_encode($channels)]);
        }

        return back()->with('success', 'QR Code berhasil dihapus.');
    }
}
