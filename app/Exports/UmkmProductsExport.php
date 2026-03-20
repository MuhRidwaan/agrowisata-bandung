<?php

namespace App\Exports;

use App\Models\UmkmProduct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UmkmProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return UmkmProduct::with('vendor')->get();
    }

    /**
     * Define the headings for the export
     */
    public function headings(): array
    {
        return [
            'ID',
            'Vendor',
            'Nama Produk',
            'Deskripsi',
            'Harga',
            'Tanggal Dibuat',
            'Terakhir Diubah',
        ];
    }

    /**
     * Map the data
     */
    public function map($product): array
    {
        return [
            $product->id,
            $product->vendor?->name,
            $product->name,
            $product->description,
            $product->price,
            $product->created_at->format('Y-m-d H:i:s'),
            $product->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
