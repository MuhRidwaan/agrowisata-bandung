<?php

namespace App\Http\Controllers;

use App\Models\UmkmProduct;
use App\Models\UmkmProductPhoto;
use App\Models\PaketTour;
use App\Models\Vendor;
use App\Http\Requests\UmkmProductRequest;
use App\Exports\UmkmProductsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UMKMProductController extends Controller
{
    /**
     * Export UMKM products to Excel
     */
    public function export()
    {
        return Excel::download(new UmkmProductsExport, 'umkm-products.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = UmkmProduct::with('vendor', 'photos');

        // Jika user adalah Vendor, hanya tampilkan produk milik mereka
        if (auth()->user()->hasRole('Vendor')) {
            $vendorId = auth()->user()->vendor->id ?? null;
            $query->where('vendor_id', $vendorId);
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by date range
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $umkmProducts = $query->latest()->paginate(10)->withQueryString();
        $vendors = Vendor::orderBy('name')->get();

        return view('backend.umkm_products.index', compact('umkmProducts', 'vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paketTours = $this->getSelectablePaketTours();

        return view('backend.umkm_products.form', [
            'umkmProduct' => new UmkmProduct(),
            'paketTours' => $paketTours,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UmkmProductRequest $request)
    {
        $data = $request->validated();
        $paketTours = $this->resolvePaketTours($data['paket_tour_ids'] ?? []);
        $product = null;

        unset($data['paket_tour_ids']);
        unset($data['path_foto']);
        $data['vendor_id'] = $paketTours->first()->vendor_id;

        DB::transaction(function () use ($request, $data, $paketTours, &$product) {
            $product = UmkmProduct::create($data);
            $product->paketTours()->sync($paketTours->pluck('id')->all());
            $this->storeUploadedPhotos($product, Arr::wrap($request->file('path_foto')));
        });

        return redirect()
            ->route('umkm-products.index')
            ->with('success', 'Produk UMKM berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit(UmkmProduct $umkmProduct)
    {
        // Authorization check for vendors
        if (auth()->user()->hasRole('Vendor')) {
            if ($umkmProduct->vendor_id !== auth()->user()->vendor->id) {
                abort(403, 'You are not authorized to edit this product.');
            }
        }

        $umkmProduct->loadMissing(['photos', 'paketTours']);
        $paketTours = $this->getSelectablePaketTours();

        return view('backend.umkm_products.form', compact('umkmProduct', 'paketTours'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(UmkmProductRequest $request, UmkmProduct $umkmProduct)
    {
        // Authorization check for vendors
        if (auth()->user()->hasRole('Vendor')) {
            if ($umkmProduct->vendor_id !== auth()->user()->vendor->id) {
                abort(403, 'You are not authorized to update this product.');
            }
        }

        $data = $request->validated();
        $paketTours = $this->resolvePaketTours($data['paket_tour_ids'] ?? []);

        unset($data['paket_tour_ids']);
        unset($data['path_foto']);
        $data['vendor_id'] = $paketTours->first()->vendor_id;

        DB::transaction(function () use ($request, $umkmProduct, $data, $paketTours) {
            $umkmProduct->update($data);
            $umkmProduct->paketTours()->sync($paketTours->pluck('id')->all());
            $this->storeUploadedPhotos($umkmProduct, Arr::wrap($request->file('path_foto')));
        });

        return redirect()
            ->route('umkm-products.index')
            ->with('success', 'Produk UMKM berhasil diupdate!');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(UmkmProduct $umkmProduct)
    {
        // Authorization check for vendors
        if (auth()->user()->hasRole('Vendor')) {
            if ($umkmProduct->vendor_id !== auth()->user()->vendor->id) {
                abort(403, 'You are not authorized to delete this product.');
            }
        }

        // Delete all photos from storage
        foreach ($umkmProduct->photos as $photo) {
            if ($photo->path_foto) {
                Storage::disk('public')->delete($photo->path_foto);
            }
            $photo->delete();
        }

        // Delete relationships
        $umkmProduct->paketTours()->detach();

        // Delete the product
        $umkmProduct->delete();

        return redirect()
            ->route('umkm-products.index')
            ->with('success', 'Produk UMKM berhasil dihapus!');
    }

    private function getSelectablePaketTours()
    {
        return PaketTour::with('vendor')
            ->when(auth()->user()->hasRole('Vendor'), function ($query) {
                $query->where('vendor_id', auth()->user()->vendor->id ?? null);
            })
            ->orderBy('nama_paket')
            ->get();
    }

    private function resolvePaketTours(array $paketTourIds)
    {
        $ids = collect($paketTourIds)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $paketTours = PaketTour::whereIn('id', $ids)->get();

        abort_if($paketTours->isEmpty(), 422, 'Paket tour yang dipilih tidak valid.');
        abort_if($paketTours->pluck('vendor_id')->unique()->count() > 1, 422, 'Semua paket tour harus berasal dari vendor yang sama.');

        return $paketTours;
    }

    private function storeUploadedPhotos(UmkmProduct $product, array $files): void
    {
        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $path = $file->store('umkm_product_photos', 'public');

            UmkmProductPhoto::create([
                'umkm_product_id' => $product->id,
                'path_foto' => $path,
            ]);
        }
    }
}
