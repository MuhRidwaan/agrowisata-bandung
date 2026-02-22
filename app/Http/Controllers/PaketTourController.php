<?php

namespace App\Http\Controllers;

use App\Models\PaketTour;
use Illuminate\Http\Request;
use App\Models\TourPackage;
use App\Models\Vendor;

class PaketTourController extends Controller
{
    public function index()
    {
        $paketTours = PaketTour::all();
        return view('backend.paket_tour.index', compact('paketTours'));
    }

    public function create()
    {
        $vendors = Vendor::pluck('name', 'id');
        return view('backend.paket_tour.form', ['paketTour' => new PaketTour(), 'vendors' => $vendors]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jam_awal' => 'required',
            'jam_akhir' => 'required',
            'vendor_id' => 'required|exists:vendors,id',
        ]);
        $data['jam_operasional'] = $data['jam_awal'] . ' - ' . $data['jam_akhir'];
        unset($data['jam_awal'], $data['jam_akhir']);
        PaketTour::create($data);
        return redirect()->route('paket-tours.index')->with('success', 'Paket Tour berhasil ditambahkan!');
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
            'deskripsi' => 'nullable|string',
            'jam_awal' => 'required',
            'jam_akhir' => 'required',
            'vendor_id' => 'required|exists:vendors,id',
        ]);
        $data['jam_operasional'] = $data['jam_awal'] . ' - ' . $data['jam_akhir'];
        unset($data['jam_awal'], $data['jam_akhir']);
        $paketTour->update($data);
        return redirect()->route('paket-tours.index')->with('success', 'Paket Tour berhasil diupdate!');
    }

    public function destroy(PaketTour $paketTour)
    {
        $paketTour->delete();
        return redirect()->route('paket-tours.index')->with('success', 'Paket Tour berhasil dihapus!');
    }
}
