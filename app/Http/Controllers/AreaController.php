<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;


class AreaController extends Controller
{
    // ================= LIST =================
    public function index()
    {
        $areas = Area::orderBy('name')->paginate(10);

        return view('backend.areas.index', compact('areas'));
    }

    // ================= FORM CREATE =================
    public function create()
    {
        return view('backend.areas.form');
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Area::create($validated);

        return redirect()->route('areas.index')
            ->with('success', 'Area berhasil ditambahkan');
    }

    // ================= FORM EDIT =================
    public function edit($id)
    {
        $area = Area::findOrFail($id);

        return view('backend.areas.form', compact('area'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $area = Area::findOrFail($id);
        $area->update($validated);

        return redirect()->route('areas.index')
            ->with('success', 'Area berhasil diupdate');
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Area berhasil dihapus');
    }
}
