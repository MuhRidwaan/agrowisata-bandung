<?php

namespace App\Http\Controllers;
use App\Models\PaketTourPhoto;
use App\Models\PaketTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaketTourPhotoController extends Controller
{
    public function destroyByPaket($paket_tour_id)
    {
        $photos = PaketTourPhoto::where('paket_tour_id', $paket_tour_id)->get();

        foreach ($photos as $photo) {
            if ($photo->path_foto) {
                Storage::disk('public')->delete($photo->path_foto);
            }

            $photo->delete();
        }

        return redirect()->route('paket-tour-photos.index')->with('success', 'Semua foto pada paket berhasil dihapus!');
    }
    
    public function index()
    {
        $user = auth()->user();
        $query = PaketTour::with('photos')->whereHas('photos');

        if ($user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
        }

        $pakets = $query->get();
        return view('backend.paket_tour_photo.index', compact('pakets'));
    }

    public function create()
    {
        $user = auth()->user();
        $query = PaketTour::query();

        if ($user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
        }

        $paketTours = $query->get();
        return view('backend.paket_tour_photo.form', ['photo' => new PaketTourPhoto(), 'paketTours' => $paketTours]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'paket_tour_id' => [
                'required',
                'exists:paket_tours,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->hasRole('Vendor')) {
                        $exists = PaketTour::where('id', $value)
                            ->where('vendor_id', $user->vendor->id ?? null)
                            ->exists();
                        if (!$exists) {
                            $fail('Paket tour yang dipilih tidak valid.');
                        }
                    }
                },
            ],
            'path_foto' => 'required|array',
            'path_foto.*' => 'required|file|image|mimes:jpeg,png,jpg,gif|max:5120',
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
        $user = auth()->user();
        $query = PaketTour::query();

        if ($user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
            // Pastikan foto yang di-edit milik vendor yang login
            if ($paketTourPhoto->paketTour->vendor_id !== $user->vendor->id) {
                abort(403, 'Akses ditolak.');
            }
        }

        $paketTours = $query->get();
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
        $user = auth()->user();
        $data = $request->validate([
            'paket_tour_id' => [
                'required',
                'exists:paket_tours,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->hasRole('Vendor')) {
                        $exists = PaketTour::where('id', $value)
                            ->where('vendor_id', $user->vendor->id ?? null)
                            ->exists();
                        if (!$exists) {
                            $fail('Paket tour yang dipilih tidak valid.');
                        }
                    }
                },
            ],
            'path_foto' => 'nullable|array',
            'path_foto.*' => 'file|image|mimes:jpeg,png,jpg,gif|max:5120',
            'delete_photos' => 'nullable|array',
            'delete_photos.*' => 'integer|exists:paket_tour_photos,id',
        ]);

        // Hapus foto yang dicentang
        if (!empty($data['delete_photos'])) {
            $photosToDelete = PaketTourPhoto::whereIn('id', $data['delete_photos'])
                ->where('paket_tour_id', $paketTourPhoto->paket_tour_id)
                ->get();

            foreach ($photosToDelete as $photo) {
                if ($photo->path_foto) {
                    Storage::disk('public')->delete($photo->path_foto);
                }

                $photo->delete();
            }
        }

        // Tambah foto baru (buat record baru, bukan replace)
        $files = $request->file('path_foto');
        if ($files) {
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('paket_tour_photos', 'public');
                    PaketTourPhoto::create([
                        'paket_tour_id' => $data['paket_tour_id'],
                        'path_foto' => $path,
                    ]);
                }
            }
        }

        // Update paket_tour_id jika berubah
        $paketTourPhoto->update(['paket_tour_id' => $data['paket_tour_id']]);

        return redirect()->route('paket-tour-photos.index')->with('success', 'Foto berhasil diupdate!');
    }

    public function destroy(PaketTourPhoto $paketTourPhoto)
    {
        if ($paketTourPhoto->path_foto) {
            Storage::disk('public')->delete($paketTourPhoto->path_foto);
        }

        $paketTourPhoto->delete();
        return redirect()->route('paket-tour-photos.index')->with('success', 'Foto berhasil dihapus!');
    }
}
