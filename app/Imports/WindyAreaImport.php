<?php

namespace App\Imports;

use App\Models\WindyArea;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class WindyAreaImport implements ToModel, WithUpserts, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use Importable, SkipsFailures;

    // Trong trường hợp dữ liệu đã tồn tại, chúng ta sẽ cập nhật dữ liệu đó
    public function uniqueBy(): string
    {
        return 'name';
    }

    // Hàng tiêu đề của file excel
    public function headingRow(): int
    {
        return 1;
    }

    // Hàm bỏ qua các hàng trống
    public function isEmptyWhen(array $row): bool
    {
        return $row['name'] === '' || $row['name'] === null;
    }

    // Hàm validate dữ liệu
    public function rules(): array
    {
        return [
            '*.name' => 'required|max:5',
            '*.wo' => 'nullable|integer',
            '*.v3s50' => 'nullable|integer',
            '*.v10m50' => 'nullable|integer',
            '*.description' => 'nullable',
        ];
    }

    // Cấu hình các message tương ứng
    public function customValidationMessages(): array
    {
        return [
            '*.name.required' => __('customValidate.windy-area.name.required'),
            '*.name.max' => __('customValidate.windy-area.name.max'),
            '*.wo.integer' => __('customValidate.windy-area.wo.integer'),
            '*.v3s50.integer' => __('customValidate.windy-area.v3s50.integer'),
            '*.v10m50.integer' => __('customValidate.windy-area.v10m50.integer'),
        ];
    }

    // Hàm tạo mới hoặc cập nhật dữ liệu
    public function model(array $row): WindyArea
    {
        return WindyArea::updateOrCreate(
            ['name' => $row['name']],
            [
                'wo'          => $row['wo'] ?? null,
                'v3s50'       => $row['v3s50'] ?? null,
                'v10m50'      => $row['v10m50'] ?? null,
                'description' => $row['description'] ?? null,
            ]
        );
    }
}
