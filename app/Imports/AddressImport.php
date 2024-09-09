<?php

namespace App\Imports;

use App\Models\Address\Commune;
use App\Models\Address\District;
use App\Models\Address\Province;
use App\Models\WindyArea;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class AddressImport implements ToModel, WithUpserts, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure, WithBatchInserts, WithChunkReading, shouldQueue
{
    use Importable, SkipsFailures;

    // Trong trường hợp dữ liệu đã tồn tại, chúng ta sẽ cập nhật dữ liệu đó
    public function uniqueBy(): string
    {
        return 'maxa';
    }
    // Hàng tiêu đề của file excel
    public function headingRow(): int
    {
        return 1;
    }
    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }


   // Hàm bỏ qua các hàng trống
    public function isEmptyWhen(array $row): bool
    {
        return ($row['maxa'] === '' ||  $row['maxa'] === null) &&
                ($row['matinh'] === '' ||  $row['matinh'] === null) &&
                ($row['mahuyen'] === '' ||  $row['mahuyen'] === null);
    }

    // Hàm validate dữ liệu
    public function rules(): array
    {
        return [
            '*.tentinh' => 'required|string|between:2,100',
            '*.matinh' => 'required|string|max:2',
            '*.tenhuyen' => 'required|string|between:2,100',
            '*.mahuyen' => 'required|string|max:3',
            '*.tenxa' => 'required|string|between:2,100',
            '*.maxa' => 'required|string|max:5',
            '*.vunggio' => 'nullable|string|max:5|exists:windy_areas,name',
        ];
    }

    // Cấu hình các message tương ứng
    public function customValidationMessages(): array
    {
        return [
            '*.tentinh.required' => __('customValidate.address.province.name.required'),
            '*.tentinh.string' => __('customValidate.address.province.name.string'),
            '*.tentinh.between' => __('customValidate.address.province.name.between'),
            '*.matinh.required' => __('customValidate.address.province.code.required'),
            '*.matinh.string' => __('customValidate.address.province.code.string'),
            '*.matinh.max' => __('customValidate.address.province.code.max'),

            '*.tenhuyen.required' => __('customValidate.address.district.name.required'),
            '*.tenhuyen.string' => __('customValidate.address.district.name.string'),
            '*.tenhuyen.between' => __('customValidate.address.district.name.between'),
            '*.mahuyen.required' => __('customValidate.address.district.code.required'),
            '*.mahuyen.string' => __('customValidate.address.district.code.string'),
            '*.mahuyen.max' => __('customValidate.address.district.code.max'),

            '*.tenxa.required' => __('customValidate.address.commune.name.required'),
            '*.tenxa.string' => __('customValidate.address.commune.name.string'),
            '*.tenxa.between' => __('customValidate.address.commune.name.between'),
            '*.maxa.required' => __('customValidate.address.commune.code.required'),
            '*.maxa.string' => __('customValidate.address.commune.code.string'),
            '*.maxa.max' => __('customValidate.address.commune.code.max'),

            '*.vunggio.string' => __('customValidate.windy-area.name.string'),
            '*.vunggio.max' => __('customValidate.windy-area.name.max'),
            '*.vunggio.exists' => __('customValidate.windy-area.name.exists'),

        ];
    }

    public function model(array $row): void
    {

        $province = Province::updateOrCreate(
            ['code' => $row['matinh']],
            [
                'name' => $row['tentinh'],
                'slug' => Str::slug($row['tentinh']),
                'country_id' => 1,
            ]
        );

        $district = District::updateOrCreate(
            ['code' => $row['mahuyen']],
            [
                'name' => $row['tenhuyen'],
                'slug' => Str::slug($row['tenhuyen']),
                'province_id' => $province->id,
            ]
        );

        $windyArea = WindyArea::where('name', $row['vunggio'])->first();
        Commune::updateOrCreate(
            ['code' => $row['maxa']],
            [
                'name' => $row['tenxa'],
                'slug' => Str::slug($row['tenxa']),
                'district_id' => $district->id,
                'windy_area_id' => $windyArea ? $windyArea->id : null,
            ]
        );
    }

}
