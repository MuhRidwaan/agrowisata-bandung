<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingReportExport implements
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
            'Date Created',
            'Booking Code',
            'Customer Name',
            'Tour Package',
            'Pax',
            'Status',
            'Total Price',
        ];
    }

    public function map($booking): array
    {
        return [
            $this->no++,
            \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i'),
            $booking->booking_code,
            $booking->customer_name,
            $booking->paketTour->nama_paket ?? '-',
            $booking->jumlah_peserta,
            strtoupper($booking->status),
            'Rp ' . number_format($booking->total_price, 0, ',', '.'),
        ];
    }
}
