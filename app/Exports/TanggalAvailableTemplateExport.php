<?php

namespace App\Exports;

use App\Models\PaketTour;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TanggalAvailableTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'paket_tour_id',
            'nama_paket',
            'tanggal',
            'kuota',
            'status',
        ];
    }

    public function array(): array
    {
        // Ambil paket tour pertama dari DB sebagai contoh
        $paket = PaketTour::first();

        if ($paket) {
            return [
                [$paket->id, $paket->nama_paket, now()->addMonth()->format('Y-m-d'), 50, 'aktif'],
                [$paket->id, $paket->nama_paket, now()->addMonth()->addDay()->format('Y-m-d'), 30, 'aktif'],
                [$paket->id, $paket->nama_paket, now()->addMonth()->addDays(2)->format('Y-m-d'), 20, 'nonaktif'],
            ];
        }

        // Fallback jika belum ada paket tour
        return [
            ['', 'Isi nama paket tour yang sudah ada', '2026-04-01', 50, 'aktif'],
        ];
    }
}
