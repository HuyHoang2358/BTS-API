<?php

namespace App\Imports;

use App\Models\Device\Vendor;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class VendorImport implements ToModel, WithUpserts, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
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
        return ($row["name"] === '' || $row["name"] === null) &&
                ($row["description"] === '' || $row["description"] === null) &&
                ($row["logo"] === '' || $row["logo"] === null);
    }

    // Hàm validate dữ liệu
    public function rules(): array
    {
        return [
            '*.name' => 'required|max:255',
            '*.description' => 'nullable|max:500',
            '*.website' => 'nullable|url|max:2048',
            '*.logo' => 'nullable|string|max:500',
        ];
    }

    // Cấu hình các message tương ứng
    public function customValidationMessages(): array
    {
        return [
            '*.name.required' => __('customValidate.device.vendor.name.required'),
            '*.name.max' => __('customValidate.device.vendor.name.max'),
            '*.description.max' => __('customValidate.device.vendor.description.max'),
            '*.website.url' => __('customValidate.device.vendor.website.url'),
            '*.website.max' => __('customValidate.device.vendor.website.max'),
            '*.logo.string' => __('customValidate.device.vendor.logo.string'),
            '*.logo.max' => __('customValidate.device.vendor.logo.max'),
        ];
    }

    // Hàm tạo mới hoặc cập nhật dữ liệu
    public function model(array $row): Vendor
    {
        return Vendor::updateOrCreate(
            ['name' => $row['name']],
            [
                'description'          => $row['description'] ?? null,
                'website'       => $row['website'] ?? null,
                'logo'      => $row['logo'] ?? null,
                'slug' => Str::slug($row['name']),
            ]
        );
    }
}
