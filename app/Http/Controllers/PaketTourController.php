<?php

namespace App\Http\Controllers;

use App\Models\PaketTour;
use Illuminate\Http\Request;

class PaketTourController extends Controller
{
    public function index()
    {
        $paketTours = PaketTour::all();
        return view('backend.paket_tour.index', compact('paketTours'));
    }

    public function create()
    {
        return view('backend.paket_tour.form', ['paketTour' => new PaketTour()]);
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
        return view('backend.paket_tour.form', compact('paketTour'));
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
