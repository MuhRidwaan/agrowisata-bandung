<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    public function collection()
    {
        return User::latest()->get();
    }

    // HEADER EXCEL
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Email',
            'Tanggal Dibuat',
        ];
    }

    // CUSTOM ISI ROW
    public function map($user): array
    {
        static $no = 1;

        return [
            $no++,
            $user->name,
            $user->email,
            $user->created_at->format('d-m-Y'),
        ];
    }
}
