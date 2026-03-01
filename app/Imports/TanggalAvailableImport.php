<?php

namespace App\Imports;

use App\Models\TanggalAvailable;
use App\Models\PaketTour;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TanggalAvailableImport implements ToCollection, WithHeadingRow
{
    public $imported = 0;
    public $skipped = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $row = $row->toArray();
            $rowNum = $index + 2; // +2 karena heading row + 0-based index

            // Support paket_tour_id langsung atau nama paket
            $paketTourId = !empty($row['paket_tour_id']) ? (int) $row['paket_tour_id'] : null;

            if (!$paketTourId && !empty($row['nama_paket'])) {
                $paket = PaketTour::where('nama_paket', trim($row['nama_paket']))->first();
                $paketTourId = $paket ? $paket->id : null;
            }

            // Skip jika paket tour tidak valid
            if (!$paketTourId || !PaketTour::where('id', $paketTourId)->exists()) {
                $this->skipped++;
                $nama = $row['nama_paket'] ?? $row['paket_tour_id'] ?? 'kosong';
                $this->errors[] = "Baris {$rowNum}: Paket tour '{$nama}' tidak ditemukan di database";
                continue;
            }

            // Support kolom tanggal atau date
            $tanggal = $row['tanggal'] ?? $row['date'] ?? null;
            if (empty($tanggal)) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: Kolom tanggal kosong";
                continue;
            }

            // Jika tanggal berupa Excel serial number, convert
            if (is_numeric($tanggal)) {
                $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggal)->format('Y-m-d');
            }

            TanggalAvailable::create([
                'paket_tour_id' => $paketTourId,
                'tanggal'       => $tanggal,
                'kuota'         => (int) ($row['kuota'] ?? $row['quota'] ?? 0),
                'status'        => $row['status'] ?? 'aktif',
            ]);

            $this->imported++;
        }
    }
}
