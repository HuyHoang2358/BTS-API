<?php

namespace App\Exports;

use App\Models\Device\Device;
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

class DeviceExport implements FromCollection,  ShouldAutoSize, WithStyles, WithHeadings, WithColumnWidths
{
    use Exportable;
    public function collection(): Collection
    {
        $devices =  Device::with([ 'category','vendor', 'params'])->orderBy('device_category_id', 'ASC')->get();
        $collections = new Collection();
        foreach ($devices as $index => $device)
        {
            $collections[] = (object)[
                "index" => $index,
                "name" => $device->name,
                "vendor" => $device->vendor ? $device->vendor->name : '',
                "category" => $device->category ? $device->category->name : '',
                'length' => $device->length,
                'width' => $device->width,
                'depth' => $device->depth,
                'weight' => $device->weight,
                'diameter' => $device->diameter,
                'description' => $device->description,
            ];
        }
        return $collections;
    }
    public function styles(Worksheet $sheet): array
    {
        $rowCount = count($this->collection()) + 1; // +1 là do có hàng tiêu đề

        $sheet->getStyle('A1:J' . $rowCount)->applyFromArray([
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
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 30,
        ];
    }

    public function headings(): array
    {
        return [
            'Index',
            'Name',
            'Vendor',
            'Category',
            'Length',
            'Width',
            'Depth',
            'Weight',
            'Diameter',
            'Description'
        ];
    }
}
