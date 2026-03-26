<?php

namespace App\Exports;

use App\Models\PaketTour;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class TanggalAvailableReferenceSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function collection()
    {
        $query = PaketTour::with('vendor')->orderBy('nama_paket');
        $user = auth()->user();

        if ($user && $user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'paket_tour_id',
            'nama_paket',
            'vendor',
            'kuota_default',
            'status_valid',
            'catatan',
        ];
    }

    public function map($paket): array
    {
        return [
            $paket->id,
            $paket->nama_paket,
            $paket->vendor->name ?? '-',
            $paket->kuota,
            'aktif / nonaktif',
            'Pilih salah satu acuan: paket_tour_id atau nama_paket saat isi sheet Template Import.',
        ];
    }

    public function title(): string
    {
        return 'Referensi Paket Tour';
    }
}
