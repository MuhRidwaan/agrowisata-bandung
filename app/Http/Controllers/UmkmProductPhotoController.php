<?php

namespace App\Http\Controllers;

use App\Models\UmkmProductPhoto;
use App\Models\UmkmProduct;
use App\Http\Requests\UmkmProductPhotoRequest;
use Illuminate\Support\Facades\Storage;

class UmkmProductPhotoController extends Controller
{
    /**
     * Store multiple photos for a product
     */
    public function store(UmkmProductPhotoRequest $request)
    {
        $data = $request->validated();
        $files = $request->file('path_foto');
        $uploadedCount = 0;

        if ($files) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('umkm_product_photos', 'public');
                    UmkmProductPhoto::create([
                        'umkm_product_id' => $data['umkm_product_id'],
                        'path_foto' => $path,
                    ]);
                    $uploadedCount++;
                }
            }
        }

        return redirect()
            ->route('umkm-products.index')
            ->with('success', "{$uploadedCount} foto berhasil diupload!");
    }

    /**
     * Delete a photo
     */
    public function destroy(UmkmProductPhoto $umkm_product_photo)
    {
        // Check authorization
        if (auth()->user()->hasRole('Vendor')) {
            if ($umkm_product_photo->umkmProduct->vendor_id !== auth()->user()->vendor->id) {
                abort(403);
            }
        }

        // Delete file from storage
        if ($umkm_product_photo->path_foto) {
            Storage::disk('public')->delete($umkm_product_photo->path_foto);
        }

        $umkm_product_photo->delete();

        return redirect()
            ->route('umkm-products.index')
            ->with('success', 'Foto berhasil dihapus!');
    }
}
