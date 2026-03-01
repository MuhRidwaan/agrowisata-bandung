<?php

namespace App\Exports;

use App\Models\TanggalAvailable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TanggalAvailableExport implements FromCollection, WithHeadings, WithMapping
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $query = TanggalAvailable::with('paketTour')->orderBy('tanggal', 'desc');

        if ($this->dateFrom) {
            $query->whereDate('tanggal', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('tanggal', '<=', $this->dateTo);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Paket Tour ID',
            'Nama Paket',
            'Tanggal',
            'Kuota',
            'Status',
            'Created At',
            'Updated At',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->paket_tour_id,
            $row->paketTour->nama_paket ?? '-',
            $row->tanggal,
            $row->kuota,
            $row->status,
            $row->created_at,
            $row->updated_at,
        ];
    }
}
