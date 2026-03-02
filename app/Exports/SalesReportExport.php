<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements
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
            'Payment Date',
            'Booking Code',
            'Customer Name',
            'Tour Package',
            'Pax',
            'Total Price',
        ];
    }

    public function map($payment): array
    {
        return [
            $this->no++,
            \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, H:i'),
            $payment->booking->booking_code,
            $payment->booking->customer_name,
            $payment->booking->paketTour->nama_paket ?? '-',
            $payment->booking->jumlah_peserta,
            'Rp ' . number_format($payment->booking->total_price, 0, ',', '.'),
        ];
    }
}
