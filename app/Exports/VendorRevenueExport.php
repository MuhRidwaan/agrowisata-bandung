<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class VendorRevenueExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected $data;
    protected $no = 1;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'No',
            'Vendor Name',
            'Total Revenue',
            'Package Details (Package: Bookings | Revenue)',
        ];
    }

    public function map($vendor): array
    {
        $details = $vendor['package_details']->map(function($p) {
            return $p['name'] . ": " . $p['bookings_count'] . " bookings | Rp " . number_format($p['revenue'], 0, ',', '.');
        })->implode("\n");

        return [
            $this->no++,
            $vendor['name'],
            'Rp ' . number_format($vendor['total_revenue'], 0, ',', '.'),
            $details
        ];
    }
}
