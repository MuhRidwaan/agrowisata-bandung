<?php

namespace App\Imports;

use App\Models\PaketTour;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PaketToursImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new PaketTour([
            'nama_paket'  => $row['package_name'],
            'deskripsi'   => $row['description'] ?? null,
            'jam_awal'    => $row['start_time'] ?? null,
            'jam_akhir'   => $row['end_time'] ?? null,
            'harga_paket' => $row['price'] ?? null,
            'kuota'       => $row['kuota'] ?? null,
            'aktivitas'   => isset($row['activities']) ? json_decode($row['activities'], true) : null,
            'vendor_id'   => $row['vendor_id'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'package_name' => 'required|string|max:255',
            'vendor_id'    => 'nullable|exists:vendors,id',
        ];
    }
}
