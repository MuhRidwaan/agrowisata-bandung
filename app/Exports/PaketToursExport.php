<?php

namespace App\Exports;

use App\Models\PaketTour;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaketToursExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return PaketTour::select('id', 'nama_paket', 'deskripsi', 'jam_awal', 'jam_akhir', 'harga_paket', 'aktivitas', 'vendor_id', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Package Name',
            'Description',
            'Start Time',
            'End Time',
            'Price',
            'Activities',
            'Vendor ID',
            'Created At',
            'Updated At',
        ];
    }
}
