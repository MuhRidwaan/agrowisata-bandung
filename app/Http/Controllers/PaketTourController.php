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
    public function index()
    {
        $paketTours = PaketTour::with('vendor')->get();  
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
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jam_awal'   => 'required|date_format:H:i',
            'jam_akhir'  => 'required|date_format:H:i|after:jam_awal',
            'harga_paket' => 'nullable|numeric|min:0',
            'aktivitas' => 'nullable|array',
            'aktivitas.*' => 'nullable|string|max:255',
            'vendor_id'  => 'required|exists:vendors,id',
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
            'nama_paket' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'jam_awal'   => 'required|date_format:H:i',
            'jam_akhir'  => 'required|date_format:H:i|after:jam_awal',
            'harga_paket' => 'nullable|numeric|min:0',
            'aktivitas' => 'nullable|array',
            'aktivitas.*' => 'nullable|string|max:255',
            'vendor_id'  => 'required|exists:vendors,id',
        ]);

        $paketTour->update($data);

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil diupdate!');
    }

    public function destroy(PaketTour $paketTour)
    {
        $paketTour->delete();

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new PaketToursExport, 'paket_tours.xlsx');
    }
}