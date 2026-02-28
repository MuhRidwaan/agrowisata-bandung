<?php

namespace App\Http\Controllers;
use App\Models\PaketTourPhoto;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class PaketTourPhotoController extends Controller
{
        public function destroyByPaket($paket_tour_id)
    {
        \App\Models\PaketTourPhoto::where('paket_tour_id', $paket_tour_id)->delete();
        return redirect()->route('paket-tour-photos.index')->with('success', 'Semua foto pada paket berhasil dihapus!');
    }
    
    public function index()
    {
        // Ambil semua paket beserta foto-fotonya
        $pakets = PaketTour::with('photos')->get();
        return view('backend.paket_tour_photo.index', compact('pakets'));
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
            'path_foto' => 'required',
            'path_foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $files = $request->file('path_foto');
        if (!$files) {
            $files = [];
        } elseif (!is_array($files)) {
            $files = [$files];
        }
        if (count($files) > 0) {
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('paket_tour_photos', 'public');
                    PaketTourPhoto::create([
                        'paket_tour_id' => $data['paket_tour_id'],
                        'path_foto' => $path,
                    ]);
                }
            }
        } else {
            return back()->withErrors(['path_foto' => 'Minimal 1 file harus diupload'])->withInput();
        }
        return redirect()->route('paket-tour-photos.index')->with('success', 'Foto berhasil ditambahkan!');
    }

    public function edit(PaketTourPhoto $paketTourPhoto)
    {
        $paketTours = PaketTour::all();
        // Ambil semua foto untuk paket ini
        $allPhotos = PaketTourPhoto::where('paket_tour_id', $paketTourPhoto->paket_tour_id)->get();
        return view('backend.paket_tour_photo.form', [
            'photo' => $paketTourPhoto,
            'paketTours' => $paketTours,
            'allPhotos' => $allPhotos
        ]);
    }

    public function update(Request $request, PaketTourPhoto $paketTourPhoto)
    {
        $data = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'path_foto' => $request->hasFile('path_foto') ? 'image|mimes:jpeg,png,jpg,gif|max:2048' : '',
            'delete_photos' => 'array',
            'delete_photos.*' => 'integer|exists:paket_tour_photos,id',
        ]);
        // Hapus foto yang dicentang
        if (!empty($data['delete_photos'])) {
            PaketTourPhoto::whereIn('id', $data['delete_photos'])->delete();
        }
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
