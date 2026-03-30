<?php

namespace App\Http\Controllers;

use App\Models\TanggalAvailable;
use App\Models\PaketTour;
use App\Imports\TanggalAvailableImport;
use App\Exports\TanggalAvailableExport;
use App\Exports\TanggalAvailableTemplateExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Facades\Excel;

class TanggalAvailableController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = TanggalAvailable::query();

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

        $tanggalAvailables = (clone $query)
            ->selectRaw("
                paket_tour_id,
                COUNT(*) as total_dates,
                SUM(kuota) as total_kuota,
                MIN(tanggal) as tanggal_awal,
                MAX(tanggal) as tanggal_akhir,
                SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as total_aktif,
                SUM(CASE WHEN status = 'nonaktif' THEN 1 ELSE 0 END) as total_nonaktif
            ")
            ->with('paketTour')
            ->groupBy('paket_tour_id')
            ->orderByRaw('MAX(tanggal) DESC')
            ->paginate(15)
            ->appends($request->query());

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
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'kuota' => 'nullable|integer|min:1',
            'status' => 'required|in:aktif,nonaktif',
            'dates' => 'nullable|array',
            'dates.*.tanggal' => 'required_with:dates|date',
            'dates.*.kuota' => 'required_with:dates|integer|min:1',
        ]);

        $startDate = Carbon::parse($validated['tanggal_mulai'])->toDateString();
        $endDate = Carbon::parse($validated['tanggal_selesai'] ?? $validated['tanggal_mulai'])->toDateString();
        $period = CarbonPeriod::create($startDate, $endDate);
        $totalDays = iterator_count($period);

        if ($totalDays > 366) {
            return back()
                ->withErrors(['tanggal_selesai' => 'Maksimal rentang tanggal adalah 1 tahun (366 hari).'])
                ->withInput();
        }

        $rangeDateMap = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $dateInRange) {
            $rangeDateMap[$dateInRange->toDateString()] = true;
        }

        $dateQuotaMap = [];
        if (!empty($validated['dates'])) {
            foreach ($validated['dates'] as $row) {
                $dateString = Carbon::parse($row['tanggal'])->toDateString();

                if (!isset($rangeDateMap[$dateString])) {
                    return back()
                        ->withErrors(['dates' => "Tanggal {$dateString} berada di luar rentang yang dipilih."])
                        ->withInput();
                }

                $dateQuotaMap[$dateString] = (int) $row['kuota'];
            }
        } else {
            if (empty($validated['kuota'])) {
                return back()
                    ->withErrors(['kuota' => 'Isi default quota atau generate date rows lalu isi quota per tanggal.'])
                    ->withInput();
            }

            foreach (CarbonPeriod::create($startDate, $endDate) as $dateInRange) {
                $dateQuotaMap[$dateInRange->toDateString()] = (int) $validated['kuota'];
            }
        }

        $requestedDates = array_keys($dateQuotaMap);
        if (empty($requestedDates)) {
            return back()
                ->withErrors(['dates' => 'Silakan generate daftar tanggal terlebih dahulu.'])
                ->withInput();
        }

        $existingDates = TanggalAvailable::where('paket_tour_id', $validated['paket_tour_id'])
            ->whereIn('tanggal', $requestedDates)
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->toArray();

        $existingDateMap = array_flip($existingDates);
        $rowsToInsert = [];
        $now = now();

        foreach ($dateQuotaMap as $dateString => $quotaValue) {
            if (isset($existingDateMap[$dateString])) {
                continue;
            }

            $rowsToInsert[] = [
                'paket_tour_id' => $validated['paket_tour_id'],
                'tanggal' => $dateString,
                'kuota' => $quotaValue,
                'status' => $validated['status'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rowsToInsert)) {
            TanggalAvailable::insert($rowsToInsert);
        }

        $imported = count($rowsToInsert);
        $skipped = count($existingDates);

        if ($imported === 0) {
            return back()
                ->withErrors(['tanggal_mulai' => 'Semua tanggal pada rentang tersebut sudah terdaftar untuk paket tour yang dipilih.'])
                ->withInput();
        }

        $message = "{$imported} tanggal berhasil ditambahkan.";
        if ($skipped > 0) {
            $message .= " {$skipped} tanggal dilewati karena sudah ada.";
        }

        return redirect()->route('tanggal-available.index')->with('success', $message);
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

        return view('backend.tanggal_available.form', [
            'tanggalAvailable' => $tanggalAvailable,
            'paketTours' => $paketTours,
            'edit' => true,
            'details' => collect(),
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

    public function editByPackage($paket_tour_id)
    {
        $user = auth()->user();
        $paketTour = PaketTour::findOrFail($paket_tour_id);

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            if ($paketTour->vendor_id !== $vendorId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $summary = TanggalAvailable::where('paket_tour_id', $paket_tour_id)
            ->selectRaw("
                COUNT(*) as total_dates,
                SUM(kuota) as total_kuota,
                MIN(tanggal) as tanggal_awal,
                MAX(tanggal) as tanggal_akhir
            ")
            ->first();

        if (!$summary || (int) $summary->total_dates === 0) {
            return redirect()->route('tanggal-available.index')->with('error', 'Data available date untuk paket ini belum tersedia.');
        }

        $dates = TanggalAvailable::where('paket_tour_id', $paket_tour_id)
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('backend.tanggal_available.form_package_edit', compact('paketTour', 'summary', 'dates'));
    }

    public function updateByPackage(Request $request, $paket_tour_id)
    {
        $user = auth()->user();
        $paketTour = PaketTour::findOrFail($paket_tour_id);

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            if ($paketTour->vendor_id !== $vendorId) {
                abort(403, 'Akses ditolak.');
            }
        }

        $validated = $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota' => 'nullable|integer|min:1',
            'dates' => 'required|array|min:1',
            'dates.*.id' => 'required|integer|distinct',
            'dates.*.kuota' => 'required|integer|min:1',
            'status' => 'nullable|in:aktif,nonaktif',
        ]);

        $startDate = Carbon::parse($validated['tanggal_mulai'])->toDateString();
        $endDate = Carbon::parse($validated['tanggal_selesai'])->toDateString();

        $ids = collect($validated['dates'])->pluck('id')->map(fn ($id) => (int) $id)->toArray();

        $records = TanggalAvailable::where('paket_tour_id', $paket_tour_id)
            ->whereIn('id', $ids)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->keyBy('id');

        if ($records->count() !== count($ids)) {
            return back()
                ->withErrors(['dates' => 'Beberapa baris tanggal tidak valid untuk paket ini. Silakan refresh halaman lalu coba lagi.'])
                ->withInput();
        }

        $statusToApply = $validated['status'] ?? null;
        $updated = 0;

        foreach ($validated['dates'] as $row) {
            $record = $records->get((int) $row['id']);
            if (!$record) {
                continue;
            }

            $record->kuota = (int) $row['kuota'];
            if (!empty($statusToApply)) {
                $record->status = $statusToApply;
            }
            $record->save();
            $updated++;
        }

        return redirect()
            ->route('tanggal-available.index')
            ->with('success', "{$updated} tanggal berhasil diupdate untuk paket {$paketTour->nama_paket}.");
    }

    public function destroyByPackage($paket_tour_id)
    {
        $user = auth()->user();

        $query = TanggalAvailable::where('paket_tour_id', $paket_tour_id);

        if ($user->hasRole('Vendor')) {
            $vendorId = $user->vendor->id ?? null;
            $isOwned = PaketTour::where('id', $paket_tour_id)
                ->where('vendor_id', $vendorId)
                ->exists();

            if (!$isOwned) {
                abort(403, 'Akses ditolak.');
            }
        }

        $deleted = $query->delete();

        if ($deleted === 0) {
            return redirect()->route('tanggal-available.index')->with('error', 'Data paket tidak ditemukan.');
        }

        return redirect()->route('tanggal-available.index')->with('success', 'Semua tanggal pada paket berhasil dihapus.');
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
