<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TanggalAvailableTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TanggalAvailableTemplateSheetExport(),
            new TanggalAvailableReferenceSheetExport(),
        ];
    }
}
