<?php

namespace App\Http\Controllers;

use App\Models\TourGallery;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourGalleryController extends Controller
{
    public function index()
    {
        $photos = TourGallery::with('tourPackage')->latest()->get();
        return view('tour_galleries.index', compact('photos'));
    }

    public function create()
    {
        $packages = TourPackage::pluck('title','id');
        return view('tour_galleries.form', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tour_package_id' => 'required|exists:tour_packages,id',
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $captions = $request->captions ?? [];
        foreach ($request->file('images') as $i => $file) {
            $path = $file->store('tour_galleries','public');
            TourGallery::create([
                'tour_package_id' => $request->tour_package_id,
                'image' => $path,
                'caption' => $captions[$i] ?? null
            ]);
        }

        return redirect()->route('tour-galleries.index')
            ->with('success','Foto berhasil diupload');
    }

    public function destroy(TourGallery $tourGallery)
    {
        // Hapus file fisik dari storage public jika ada
        if ($tourGallery->image && Storage::disk('public')->exists($tourGallery->image)) {
            Storage::disk('public')->delete($tourGallery->image);
        }
        $tourGallery->delete();
        return back()->with('success','Foto dihapus');
    }
}
