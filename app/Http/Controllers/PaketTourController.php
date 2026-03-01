<?php
namespace App\Http\Controllers;

use App\Models\PaketTour;
use App\Imports\PaketToursImport;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Exports\PaketToursExport;
use Maatwebsite\Excel\Facades\Excel;

class PaketTourController extends Controller
{
    public function show(PaketTour $paketTour)
    {
        $paketTour->load('vendor', 'tanggalAvailables');
        return view('backend.paket_tour.show', compact('paketTour'));
    }
    public function index(Request $request)
    {
        $query = PaketTour::with('vendor', 'tanggalAvailables');

        if ($request->filled('search')) {
            $query->where('nama_paket', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $paketTours = $query->get();
        return view('backend.paket_tour.index', compact('paketTours'));
    }

    public function create()
    {
        $vendors = Vendor::pluck('name', 'id');
        return view('backend.paket_tour.form', [
            'paketTour' => new PaketTour(),
            'vendors' => $vendors
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_paket'  => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'jam_awal'    => 'required|date_format:H:i',
            'jam_akhir'   => 'required|date_format:H:i|after:jam_awal',
            'harga_paket' => 'required|numeric|min:0',
            'aktivitas'   => 'required|array|min:1',
            'aktivitas.*' => 'required|string|max:255',
            'vendor_id'   => 'required|exists:vendors,id',
        ]);

        PaketTour::create($data);

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil ditambahkan!');
    }

    public function edit(PaketTour $paketTour)
    {
        $vendors = Vendor::pluck('name', 'id');
        return view('backend.paket_tour.form', compact('paketTour', 'vendors'));
    }

    public function update(Request $request, PaketTour $paketTour)
    {
        $data = $request->validate([
            'nama_paket'  => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'jam_awal'    => 'required|date_format:H:i',
            'jam_akhir'   => 'required|date_format:H:i|after:jam_awal',
            'harga_paket' => 'required|numeric|min:0',
            'aktivitas'   => 'required|array|min:1',
            'aktivitas.*' => 'required|string|max:255',
            'vendor_id'   => 'required|exists:vendors,id',
        ]);

        $paketTour->update($data);

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil diupdate!');
    }

    public function destroy(PaketTour $paketTour)
    {
        // Hapus file foto dari storage sebelum delete record
        foreach ($paketTour->photos as $photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->path_foto);
        }

        $paketTour->delete();

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new PaketToursExport, 'paket_tours.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new PaketToursImport, $request->file('file'));
            return redirect()
                ->route('paket-tours.index')
                ->with('success', 'Data Paket Tour berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}