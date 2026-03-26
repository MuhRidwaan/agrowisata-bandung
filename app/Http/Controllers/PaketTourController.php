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

        // Jika user adalah Vendor, hanya tampilkan paket milik mereka
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
        }

        if ($request->filled('search')) {
            $query->where('nama_paket', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $paketTours = $query->latest()->paginate(10)->withQueryString();
        $vendors = Vendor::orderBy('name')->get();
        
        return view('backend.paket_tour.index', compact('paketTours', 'vendors'));
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
        $rules = [
            'nama_paket'  => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'jam_awal'    => 'required|date_format:H:i',
            'jam_akhir'   => 'required|date_format:H:i|after:jam_awal',
            'harga_paket' => 'required|numeric|min:0',
            'is_bundling_available' => 'nullable|boolean',
            'harga_bundling' => 'nullable|required_if:is_bundling_available,1|numeric|min:0',
            'bundling_people' => 'nullable|required_if:is_bundling_available,1|integer|min:1',
            'aktivitas'   => 'required|array|min:1',
            'aktivitas.*' => 'required|string|max:255',
        ];

        if (auth()->user()->hasRole('Super Admin')) {
            $rules['vendor_id'] = 'required|exists:vendors,id';
        }

        $data = $request->validate($rules);
        
        // Convert checkbox value to boolean
        $data['is_bundling_available'] = $request->has('is_bundling_available') ? true : false;
        $data['harga_bundling'] = $data['is_bundling_available'] ? ($data['harga_bundling'] ?? null) : null;
        $data['bundling_people'] = $data['is_bundling_available'] ? ($data['bundling_people'] ?? null) : null;

        if (auth()->user()->hasRole('Vendor')) {
            $data['vendor_id'] = auth()->user()->vendor->id;
        }

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
            'is_bundling_available' => 'nullable|boolean',
            'harga_bundling' => 'nullable|required_if:is_bundling_available,1|numeric|min:0',
            'bundling_people' => 'nullable|required_if:is_bundling_available,1|integer|min:1',
            'aktivitas'   => 'required|array|min:1',
            'aktivitas.*' => 'required|string|max:255',
            'vendor_id'   => 'required|exists:vendors,id',
        ]);
        
        // Convert checkbox value to boolean
        $data['is_bundling_available'] = $request->has('is_bundling_available') ? true : false;
        $data['harga_bundling'] = $data['is_bundling_available'] ? ($data['harga_bundling'] ?? null) : null;
        $data['bundling_people'] = $data['is_bundling_available'] ? ($data['bundling_people'] ?? null) : null;

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
