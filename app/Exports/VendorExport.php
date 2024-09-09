<?php

namespace App\Exports;

use App\Models\Device\Vendor;
use App\Models\WindyArea;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorExport implements FromCollection, ShouldAutoSize, WithStyles, WithHeadings, WithColumnWidths
{
    use Exportable;
    public function collection(): Collection
    {
        return Vendor::select('id', 'name', 'description', 'logo', 'website')->get();
    }

    public function styles(Worksheet $sheet): array
    {
        $rowCount = count($this->collection()) + 1; // +1 là do có hàng tiêu đề

        $sheet->getStyle('A1:E' . $rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Màu đen cho border
                ],
            ],
        ]);

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => '000000'], // Text màu đen
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER, // Căn giữa ngang
                    'vertical' => Alignment::VERTICAL_CENTER, // Căn giữa dọc
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'E6B8B7', // Màu nền #E6B8B7
                    ],
                ],
            ],
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 50,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Logo',
            'Website',
        ];
    }

}
