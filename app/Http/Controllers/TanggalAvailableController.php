<?php

namespace App\Http\Controllers;

use App\Models\TanggalAvailable;
use App\Models\PaketTour;
use App\Imports\TanggalAvailableImport;
use App\Exports\TanggalAvailableExport;
use App\Exports\TanggalAvailableTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TanggalAvailableController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TanggalAvailable::with('paketTour')->orderBy('tanggal', 'desc');

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            $query->whereHas('paketTour', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('tanggal', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('tanggal', '<=', $request->date_to);
        }

        $tanggalAvailables = $query->paginate(15)->appends($request->query());
        return view('backend.tanggal_available.index', compact('tanggalAvailables'));
    }

    public function create()
    {
        $user = auth()->user();
        $query = PaketTour::query();

        if ($user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
        }

        $paketTours = $query->get();
        return view('backend.tanggal_available.form', [
            'tanggalAvailable' => new TanggalAvailable(),
            'paketTours' => $paketTours,
            'edit' => false,
            'details' => collect(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
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
            'tanggal' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Cek duplikat tanggal untuk paket yang sama
        $exists = TanggalAvailable::where('paket_tour_id', $validated['paket_tour_id'])
            ->where('tanggal', $validated['tanggal'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Tanggal ini sudah terdaftar untuk paket tour yang dipilih.'])->withInput();
        }

        TanggalAvailable::create($validated);
        return redirect()->route('tanggal-available.index')->with('success', 'Tanggal berhasil ditambahkan.');
    }

    public function edit(TanggalAvailable $tanggalAvailable)
    {
        $user = auth()->user();
        $query = PaketTour::query();

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
            // Pastikan data yang diedit milik vendor yang login
            if ($tanggalAvailable->paketTour->vendor_id !== $vendorId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $paketTours = $query->get();

        // Ambil semua available dates untuk paket tour yang sama (detail view-only)
        $details = TanggalAvailable::where('paket_tour_id', $tanggalAvailable->paket_tour_id)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('backend.tanggal_available.form', [
            'tanggalAvailable' => $tanggalAvailable,
            'paketTours' => $paketTours,
            'edit' => true,
            'details' => $details,
        ]);
    }

    public function update(Request $request, TanggalAvailable $tanggalAvailable)
    {
        $user = auth()->user();
        $validated = $request->validate([
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
            'tanggal' => 'required|date',
            'kuota' => 'required|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Cek duplikat tanggal untuk paket yang sama (kecuali record ini sendiri)
        $exists = TanggalAvailable::where('paket_tour_id', $validated['paket_tour_id'])
            ->where('tanggal', $validated['tanggal'])
            ->where('id', '!=', $tanggalAvailable->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Tanggal ini sudah terdaftar untuk paket tour yang dipilih.'])->withInput();
        }

        $tanggalAvailable->update($validated);
        return redirect()->route('tanggal-available.index')->with('success', 'Tanggal berhasil diupdate.');
    }

    public function destroy(TanggalAvailable $tanggalAvailable)
    {
        $tanggalAvailable->delete();
        return redirect()->route('tanggal-available.index')->with('success', 'Tanggal berhasil dihapus.');
    }

    public function export(Request $request)
    {
        return Excel::download(
            new TanggalAvailableExport($request->date_from, $request->date_to),
            'available_dates.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new TanggalAvailableImport;
            Excel::import($import, $request->file('file'));

            if ($import->imported > 0) {
                $msg = "{$import->imported} data berhasil diimport!";
                if ($import->skipped > 0) {
                    $msg .= " ({$import->skipped} baris dilewati)";
                }
                return redirect()
                    ->route('tanggal-available.index')
                    ->with('success', $msg);
            } else {
                $detail = implode('; ', array_slice($import->errors, 0, 5));
                return redirect()
                    ->back()
                    ->with('error', "Tidak ada data yang berhasil diimport. Detail: {$detail}");
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TanggalAvailableTemplateExport, 'template_available_date.xlsx');
    }
}
