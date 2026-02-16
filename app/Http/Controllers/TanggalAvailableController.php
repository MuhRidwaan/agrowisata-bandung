<?php

namespace App\Http\Controllers;

use App\Models\TanggalAvailable;
use App\Models\PaketTour;
use Illuminate\Http\Request;

class TanggalAvailableController extends Controller
{
    public function index()
    {
        $tanggalAvailables = TanggalAvailable::with('paketTour')->orderBy('tanggal', 'desc')->paginate(15);
        return view('backend.tanggal_available.index', compact('tanggalAvailables'));
    }

    public function create()
    {
        $paketTours = PaketTour::all();
        return view('backend.tanggal_available.form', [
            'tanggalAvailable' => new TanggalAvailable(),
            'paketTours' => $paketTours,
            'edit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'tanggal' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);
        TanggalAvailable::create($validated);
        return redirect()->route('tanggal-available.index')->with('success', 'Tanggal berhasil ditambahkan.');
    }

    public function edit(TanggalAvailable $tanggalAvailable)
    {
        $paketTours = PaketTour::all();
        return view('backend.tanggal_available.form', [
            'tanggalAvailable' => $tanggalAvailable,
            'paketTours' => $paketTours,
            'edit' => true,
        ]);
    }

    public function update(Request $request, TanggalAvailable $tanggalAvailable)
    {
        $validated = $request->validate([
            'paket_tour_id' => 'required|exists:paket_tours,id',
            'tanggal' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);
        $tanggalAvailable->update($validated);
        return redirect()->route('tanggal-available.index')->with('success', 'Tanggal berhasil diupdate.');
    }

    public function destroy(TanggalAvailable $tanggalAvailable)
    {
        $tanggalAvailable->delete();
        return redirect()->route('tanggal-available.index')->with('success', 'Tanggal berhasil dihapus.');
    }
}
