<?php

namespace App\Exports;

use App\Models\PaketTour;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TanggalAvailableTemplateSheetExport implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'paket_tour_id',
            'nama_paket',
            'tanggal',
            'kuota',
            'status',
            'catatan',
        ];
    }

    public function array(): array
    {
        $paket = $this->paketTourQuery()->first();

        if ($paket) {
            return [
                [
                    $paket->id,
                    $paket->nama_paket,
                    now()->addMonth()->format('Y-m-d'),
                    50,
                    'aktif',
                    'Gunakan paket_tour_id atau nama_paket yang ada di sheet Referensi Paket Tour.',
                ],
                [
                    $paket->id,
                    $paket->nama_paket,
                    now()->addMonth()->addDay()->format('Y-m-d'),
                    30,
                    'aktif',
                    'Format tanggal wajib YYYY-MM-DD. Contoh: 2026-04-15.',
                ],
                [
                    $paket->id,
                    $paket->nama_paket,
                    now()->addMonth()->addDays(2)->format('Y-m-d'),
                    20,
                    'nonaktif',
                    'Status yang didukung: aktif atau nonaktif.',
                ],
            ];
        }

        return [
            [
                '',
                '',
                '2026-04-01',
                50,
                'aktif',
                'Buat paket tour terlebih dahulu, lalu lihat sheet Referensi Paket Tour untuk ID dan nama paket yang valid.',
            ],
        ];
    }

    public function title(): string
    {
        return 'Template Import';
    }

    protected function paketTourQuery()
    {
        $query = PaketTour::query()->orderBy('nama_paket');
        $user = auth()->user();

        if ($user && $user->hasRole('Vendor')) {
            $query->where('vendor_id', $user->vendor->id ?? null);
        }

        return $query;
    }
}
