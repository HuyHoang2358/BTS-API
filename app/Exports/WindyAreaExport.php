<?php

namespace App\Exports;

use App\Models\WindyArea;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class WindyAreaExport implements FromCollection, ShouldAutoSize, WithStyles, WithHeadings, WithColumnWidths
{
    use Exportable;
    public function collection(): Collection
    {
        return WindyArea::all();
    }

    public function styles(Worksheet $sheet): array
    {
        $rowCount = count($this->collection()) + 1; // +1 là do có hàng tiêu đề

        $sheet->getStyle('A1:F' . $rowCount)->applyFromArray([
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
            'Tên vùng gió',
            'Wo',
            'V3S50',
            'V10M50',
            'Mô tả',
        ];
    }
}
