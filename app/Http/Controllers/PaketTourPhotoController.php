<?php

namespace App\Http\Controllers;

use App\Models\PaketTourPhoto;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PaketTourPhotoController extends Controller
{
    public function index()
    {
        $photos = PaketTourPhoto::with('paketTour')->get();
        return view('backend.paket_tour_photo.index', compact('photos'));
    }

    public function create()
    {
        $paketTours = PaketTour::all();
        return view('backend.paket_tour_photo.form', ['photo' => new PaketTourPhoto(), 'paketTours' => $paketTours]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'path_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('path_foto')) {
            $file = $request->file('path_foto');
            $path = $file->store('paket_tour_photos', 'public');
            $data['path_foto'] = $path;
        }
        PaketTourPhoto::create($data);
        return redirect()->route('paket-tour-photos.index')->with('success', 'Foto berhasil ditambahkan!');
    }

    public function edit(PaketTourPhoto $paketTourPhoto)
    {
        $paketTours = PaketTour::all();
        return view('backend.paket_tour_photo.form', ['photo' => $paketTourPhoto, 'paketTours' => $paketTours]);
    }

    public function update(Request $request, PaketTourPhoto $paketTourPhoto)
    {
        $data = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'path_foto' => $request->hasFile('path_foto') ? 'image|mimes:jpeg,png,jpg,gif|max:2048' : '',
        ]);
        if ($request->hasFile('path_foto')) {
            $file = $request->file('path_foto');
            $path = $file->store('paket_tour_photos', 'public');
            $data['path_foto'] = $path;
        } else {
            unset($data['path_foto']);
        }
        $paketTourPhoto->update($data);
        return redirect()->route('paket-tour-photos.index')->with('success', 'Foto berhasil diupdate!');
    }

    public function destroy(PaketTourPhoto $paketTourPhoto)
    {
        $paketTourPhoto->delete();
        return redirect()->route('paket-tour-photos.index')->with('success', 'Foto berhasil dihapus!');
    }
}
