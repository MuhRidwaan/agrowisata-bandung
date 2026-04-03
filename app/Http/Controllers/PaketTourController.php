<?php
namespace App\Http\Controllers;

use App\Models\PaketTour;
use App\Imports\PaketToursImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Exports\PaketToursExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;

class PaketTourController extends Controller
{
    public function show(PaketTour $paketTour)
    {
        $this->authorizePaketTourAccess($paketTour);

        $paketTour->load('vendor', 'tanggalAvailables', 'bundlings.photos');
        return view('backend.paket_tour.show', compact('paketTour'));
    }
    public function index(Request $request)
    {
        $query = PaketTour::with('vendor', 'tanggalAvailables', 'bundlings.photos');

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
            'vendors' => $vendors,
            'bundlings' => collect([[
                'id' => null,
                'label' => null,
                'people_count' => null,
                'bundle_price' => null,
                'description' => null,
                'is_active' => true,
                'photos' => collect(),
                'delete_photo_ids' => [],
            ]]),
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
            'has_minimum_person' => 'nullable|boolean',
            'minimum_person' => 'nullable|integer|min:1|required_if:has_minimum_person,1',
            'aktivitas'   => 'required|array|min:1',
            'aktivitas.*' => 'required|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'nullable|string|max:255',
            'bundlings' => 'nullable|array',
            'bundlings.*.label' => 'nullable|string|max:255',
            'bundlings.*.people_count' => 'nullable|integer|min:1',
            'bundlings.*.bundle_price' => 'nullable|numeric|min:0',
            'bundlings.*.description' => 'nullable|string',
            'bundlings.*.is_active' => 'nullable|boolean',
            'bundlings.*.photos' => 'nullable|array',
            'bundlings.*.photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'bundlings.*.delete_photo_ids' => 'nullable|array',
            'bundlings.*.delete_photo_ids.*' => 'nullable|integer|exists:paket_tour_bundling_photos,id',
        ];

        if (auth()->user()->hasRole('Super Admin')) {
            $rules['vendor_id'] = 'required|exists:vendors,id';
        }

        $data = $request->validate($rules);
        $data['facilities'] = collect($data['facilities'] ?? [])
            ->map(fn ($item) => is_string($item) ? trim($item) : $item)
            ->filter(fn ($item) => filled($item))
            ->values()
            ->all();
        
        // Convert checkbox value to boolean
        $bundlings = $this->extractBundlings($request);
        $this->validateBundlings($bundlings);

        if (auth()->user()->hasRole('Vendor')) {
            $data['vendor_id'] = auth()->user()->vendor->id;
        }

        $data['has_minimum_person'] = $request->boolean('has_minimum_person');
        $data['minimum_person'] = $data['has_minimum_person']
            ? (int) $request->input('minimum_person')
            : null;

        [$data['harga_bundling'], $data['bundling_people']] = $this->legacyBundlingValues($bundlings);
        $data['is_bundling_available'] = !empty($bundlings);

        $paketTour = PaketTour::create($data);
        $this->syncBundlings($paketTour, $bundlings, $request);

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil ditambahkan!');
    }

    public function edit(PaketTour $paketTour)
    {
        $this->authorizePaketTourAccess($paketTour);

        $vendors = Vendor::pluck('name', 'id');
        $existingBundlings = $paketTour->bundlings()->with('photos')->get()->keyBy('id');

        $bundlings = old('bundlings')
            ? collect(old('bundlings'))->map(function ($bundling) use ($existingBundlings) {
                $bundlingId = isset($bundling['id']) && $bundling['id'] !== '' ? (int) $bundling['id'] : null;
                $existing = $bundlingId ? $existingBundlings->get($bundlingId) : null;

                $bundling['photos'] = $existing?->photos ?? collect();
                $bundling['delete_photo_ids'] = $bundling['delete_photo_ids'] ?? [];

                return $bundling;
            })
            : $existingBundlings->map(function ($bundling) {
                return [
                    'id' => $bundling->id,
                    'label' => $bundling->label,
                    'people_count' => $bundling->people_count,
                    'bundle_price' => $bundling->bundle_price,
                    'description' => $bundling->description,
                    'is_active' => $bundling->is_active,
                    'photos' => $bundling->photos,
                    'delete_photo_ids' => [],
                ];
            });

        if ($bundlings->isEmpty()) {
            $bundlings = collect([[
                'id' => null,
                'label' => null,
                'people_count' => null,
                'bundle_price' => null,
                'description' => null,
                'is_active' => true,
                'photos' => collect(),
                'delete_photo_ids' => [],
            ]]);
        }

        return view('backend.paket_tour.form', compact('paketTour', 'vendors', 'bundlings'));
    }

    public function update(Request $request, PaketTour $paketTour)
    {
        $this->authorizePaketTourAccess($paketTour);

        $data = $request->validate([
            'nama_paket'  => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'jam_awal'    => 'required|date_format:H:i',
            'jam_akhir'   => 'required|date_format:H:i|after:jam_awal',
            'harga_paket' => 'required|numeric|min:0',
            'has_minimum_person' => 'nullable|boolean',
            'minimum_person' => 'nullable|integer|min:1|required_if:has_minimum_person,1',
            'aktivitas'   => 'required|array|min:1',
            'aktivitas.*' => 'required|string|max:255',
            'facilities' => 'nullable|array',
            'facilities.*' => 'nullable|string|max:255',
            'vendor_id'   => 'required|exists:vendors,id',
            'bundlings' => 'nullable|array',
            'bundlings.*.label' => 'nullable|string|max:255',
            'bundlings.*.people_count' => 'nullable|integer|min:1',
            'bundlings.*.bundle_price' => 'nullable|numeric|min:0',
            'bundlings.*.description' => 'nullable|string',
            'bundlings.*.is_active' => 'nullable|boolean',
            'bundlings.*.photos' => 'nullable|array',
            'bundlings.*.photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'bundlings.*.delete_photo_ids' => 'nullable|array',
            'bundlings.*.delete_photo_ids.*' => 'nullable|integer|exists:paket_tour_bundling_photos,id',
        ]);
        $data['facilities'] = collect($data['facilities'] ?? [])
            ->map(fn ($item) => is_string($item) ? trim($item) : $item)
            ->filter(fn ($item) => filled($item))
            ->values()
            ->all();
        
        // Convert checkbox value to boolean
        $bundlings = $this->extractBundlings($request);
        $this->validateBundlings($bundlings);

        if (auth()->user()->hasRole('Vendor')) {
            $data['vendor_id'] = auth()->user()->vendor->id;
        }

        $data['has_minimum_person'] = $request->boolean('has_minimum_person');
        $data['minimum_person'] = $data['has_minimum_person']
            ? (int) $request->input('minimum_person')
            : null;

        [$data['harga_bundling'], $data['bundling_people']] = $this->legacyBundlingValues($bundlings);
        $data['is_bundling_available'] = !empty($bundlings);

        $paketTour->update($data);
        $this->syncBundlings($paketTour, $bundlings, $request);

        return redirect()
            ->route('paket-tours.index')
            ->with('success', 'Paket Tour berhasil diupdate!');
    }

    public function destroy(PaketTour $paketTour)
    {
        $this->authorizePaketTourAccess($paketTour);

        // Hapus file foto dari storage sebelum delete record
        foreach ($paketTour->photos as $photo) {
            Storage::disk('public')->delete($photo->path_foto);
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

    private function authorizePaketTourAccess(PaketTour $paketTour): void
    {
        if (! auth()->user()->hasRole('Vendor')) {
            return;
        }

        $vendorId = auth()->user()->vendor->id ?? null;

        if ($paketTour->vendor_id !== $vendorId) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function extractBundlings(Request $request): array
    {
        return collect($request->input('bundlings', []))
            ->map(function ($bundling, $index) {
                return [
                    'source_index' => $index,
                    'label' => filled($bundling['label'] ?? null) ? trim((string) $bundling['label']) : null,
                    'id' => isset($bundling['id']) && $bundling['id'] !== ''
                        ? (int) $bundling['id']
                        : null,
                    'people_count' => isset($bundling['people_count']) && $bundling['people_count'] !== ''
                        ? (int) $bundling['people_count']
                        : null,
                    'bundle_price' => isset($bundling['bundle_price']) && $bundling['bundle_price'] !== ''
                        ? (float) $bundling['bundle_price']
                        : null,
                    'description' => filled($bundling['description'] ?? null) ? trim((string) $bundling['description']) : null,
                    'is_active' => filter_var($bundling['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
                    'delete_photo_ids' => collect($bundling['delete_photo_ids'] ?? [])
                        ->filter(fn ($id) => $id !== null && $id !== '')
                        ->map(fn ($id) => (int) $id)
                        ->values()
                        ->all(),
                    'sort_order' => $index,
                ];
            })
            ->filter(function ($bundling) {
                return $bundling['people_count'] !== null
                    || $bundling['bundle_price'] !== null
                    || $bundling['label'] !== null
                    || $bundling['description'] !== null;
            })
            ->values()
            ->all();
    }

    private function validateBundlings(array $bundlings): void
    {
        foreach ($bundlings as $index => $bundling) {
            $row = $index + 1;

            if ($bundling['people_count'] === null || $bundling['bundle_price'] === null) {
                throw ValidationException::withMessages([
                    "bundlings.{$index}.people_count" => "Bundling baris {$row} harus mengisi jumlah orang dan harga bundling.",
                ]);
            }
        }
    }

    private function syncBundlings(PaketTour $paketTour, array $bundlings, Request $request): void
    {
        $existingBundlings = $paketTour->bundlings()->with('photos')->get()->keyBy('id');
        $keptIds = collect($bundlings)
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach ($existingBundlings as $existingBundling) {
            if (! in_array($existingBundling->id, $keptIds, true)) {
                $this->deleteBundlingPhotos($existingBundling);
                $existingBundling->delete();
            }
        }

        if (empty($bundlings)) {
            return;
        }

        foreach ($bundlings as $index => $bundling) {
            $payload = [
                'label' => $bundling['label'],
                'people_count' => $bundling['people_count'],
                'bundle_price' => $bundling['bundle_price'],
                'description' => $bundling['description'],
                'is_active' => $bundling['is_active'],
                'sort_order' => $bundling['sort_order'],
            ];

            $bundlingModel = null;

            if (! empty($bundling['id']) && $existingBundlings->has($bundling['id'])) {
                $bundlingModel = $existingBundlings->get($bundling['id']);
                $bundlingModel->update($payload);
            } else {
                $bundlingModel = $paketTour->bundlings()->create($payload);
            }

            $this->syncBundlingPhotos($bundlingModel, $bundling, $request);
        }
    }

    private function syncBundlingPhotos($bundlingModel, array $bundling, Request $request): void
    {
        Log::info('Bundling photo sync started', [
            'paket_tour_id' => $bundlingModel->paket_tour_id,
            'bundling_id' => $bundlingModel->id,
            'source_index' => $bundling['source_index'] ?? null,
            'request_has_file' => $request->hasFile('bundlings'),
            'all_files_keys' => array_keys($request->allFiles()),
        ]);

        if (! empty($bundling['delete_photo_ids'])) {
            $photosToDelete = $bundlingModel->photos()
                ->whereIn('id', $bundling['delete_photo_ids'])
                ->get();

            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->path_foto);
                $photo->delete();
            }
        }

        $sourceIndex = $bundling['source_index'] ?? null;
        $uploadedPhotos = data_get($request->allFiles(), "bundlings.{$sourceIndex}.photos", []);

        if (empty($uploadedPhotos)) {
            $uploadedPhotos = $request->file("bundlings.{$sourceIndex}.photos", []);
        }

        if (! is_array($uploadedPhotos)) {
            $uploadedPhotos = $uploadedPhotos ? [$uploadedPhotos] : [];
        }

        Log::info('Bundling photo payload inspected', [
            'bundling_id' => $bundlingModel->id,
            'source_index' => $sourceIndex,
            'uploaded_count' => count($uploadedPhotos),
            'uploaded_names' => collect($uploadedPhotos)->map(function ($file) {
                return $file ? $file->getClientOriginalName() : null;
            })->filter()->values()->all(),
        ]);

        $sortOrder = (int) $bundlingModel->photos()->max('sort_order');

        foreach ($uploadedPhotos as $file) {
            if (! $file || ! $file->isValid()) {
                Log::warning('Bundling photo skipped because file is invalid', [
                    'bundling_id' => $bundlingModel->id,
                    'source_index' => $sourceIndex,
                ]);
                continue;
            }

            $sortOrder++;
            $path = $file->store('paket_tour_bundling_photos', 'public');

            Log::info('Bundling photo stored on disk', [
                'bundling_id' => $bundlingModel->id,
                'path' => $path,
                'sort_order' => $sortOrder,
            ]);

            $bundlingModel->photos()->create([
                'path_foto' => $path,
                'sort_order' => $sortOrder,
            ]);
        }

        Log::info('Bundling photo sync finished', [
            'bundling_id' => $bundlingModel->id,
            'photos_in_db' => $bundlingModel->photos()->count(),
        ]);
    }

    private function deleteBundlingPhotos($bundlingModel): void
    {
        foreach ($bundlingModel->photos as $photo) {
            Storage::disk('public')->delete($photo->path_foto);
        }
    }

    private function legacyBundlingValues(array $bundlings): array
    {
        if (! empty($bundlings)) {
            return [
                $bundlings[0]['bundle_price'],
                $bundlings[0]['people_count'],
            ];
        }

        return [
            null,
            null,
        ];
    }
}
