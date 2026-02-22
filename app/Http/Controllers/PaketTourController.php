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
            'jam_operasional' => 'nullable|string',
            'harga_paket' => 'required|numeric',
            'kuota' => 'nullable|integer',
        ]);
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
            'jam_operasional' => 'nullable|string',
            'harga_paket' => 'required|numeric',
            'kuota' => 'nullable|integer',
        ]);
        $paketTour->update($data);
        return redirect()->route('paket-tours.index')->with('success', 'Paket Tour berhasil diupdate!');
    }

    public function destroy(PaketTour $paketTour)
    {
        $paketTour->delete();
        return redirect()->route('paket-tours.index')->with('success', 'Paket Tour berhasil dihapus!');
    }
}
