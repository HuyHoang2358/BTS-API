<?php

namespace App\Imports;

use App\Models\Device\Device;
use App\Models\Device\DeviceCategory;
use App\Models\Device\Vendor;
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

class DeviceImport implements ToModel, WithUpserts, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure, WithBatchInserts, WithChunkReading /*shouldQueue*/
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
        return ($row['name'] === '' ||  $row['name'] === null) &&
            ($row['vendor'] === '' ||  $row['vendor'] === null) &&
            ($row['category'] === '' ||  $row['category'] === null);
    }

    // Hàm validate dữ liệu
    public function rules(): array
    {
        return [
            '*.name' => 'required|max:500',
            '*.description' => 'nullable|max:1000',
            '*.images' => 'nullable|max:1000',
            '*.model_url' => 'nullable|max:2048',
            '*.length' => 'nullable|numeric|between:0,1000000',
            '*.width' => 'nullable|numeric|between:0,1000000',
            '*.depth' => 'nullable|numeric|between:0,1000000',
            '*.weight' => 'nullable|numeric|between:0,1000000',
            '*.diameter' => 'nullable|numeric|between:0,1000000',
            '*.vendor' => 'nullable|exists:vendors,name',
            '*.category' => 'required|exists:device_categories,name',
            '*.params' => 'nullable|string|max:1000|regex:/^(\s*\w+\s*:\s*\w+)(\s*,\s*\w+\s*:\s*\w+)*$/',
        ];
    }

    // Cấu hình các message tương ứng
    public function customValidationMessages(): array
    {
        return [
            '*.name.required' => __('customValidate.device.name.required'),
            '*.name.max' => __('customValidate.device.name.max'),
            '*.description.max' => __('customValidate.device.description.max'),
            '*.images.max' => __('customValidate.device.image.max'),
            '*.model_url.max' => __('customValidate.device.model_url.max'),
            '*.length.numeric' => __('customValidate.device.length.numeric'),
            '*.length.between' => __('customValidate.device.length.between'),
            '*.width.numeric' => __('customValidate.device.width.numeric'),
            '*.width.between' => __('customValidate.device.width.between'),
            '*.depth.numeric' => __('customValidate.device.depth.numeric'),
            '*.depth.between' => __('customValidate.device.depth.between'),
            '*.weight.numeric' => __('customValidate.device.weight.numeric'),
            '*.weight.between' => __('customValidate.device.weight.between'),
            '*.diameter.numeric' => __('customValidate.device.diameter.numeric'),
            '*.diameter.between' => __('customValidate.device.diameter.between'),
            '*.category.required' => __('customValidate.device.device_category_id.required'),
            '*.category.exists' => __('customValidate.device.category.name.exists'),
            '*.vendor.exists' => __('customValidate.device.vendor.name.exists'),
            '*.params.string' => __('customValidate.device.params.string'),
            '*.params.max' => __('customValidate.device.params.max'),
            '*.params.regex' => __('customValidate.device.params.regex'),
        ];
    }

    public function model(array $row): Device
    {
        $deviceCategory = DeviceCategory::where('name', $row['category'])->first();
        if ($row['vendor'] !== null) $vendor = Vendor::where('name', $row['vendor'])->first();

        $device = Device::updateOrCreate(
            ["name" => $row["name"]],
            [
                'slug' => Str::slug($row['name']),
                'images' => $row['images'] ?  '/public/BTS/Images/Devices/'.$row['images']  : null,
                'model_url' => $row['model_url'] ?? null,
                'length' => $row['length'] ?? null,
                'width' => $row['width'] ?? null,
                'depth' => $row['depth'] ?? null,
                'weight' => $row['weight'] ?? null,
                'diameter' => $row['diameter'] ?? null,
                'description' => $row['description'] ?? null,
                'device_category_id' => $deviceCategory->id,
                'vendor_id' => $vendor->id ?? null,
            ]
        );
        $params_str = $row['params'] ?? '';
        $device->params()->delete();
        if (!empty($params_str)){
            $pairs = array_map('trim', explode(',', $params_str));
            foreach ($pairs as $pair) {
                if (!str_contains($pair, ':')) continue;
                list($key, $value) = explode(':', $pair, 2) + [null, null];
                if ($key !== null && $value !== null) {
                    $key = trim($key);
                    $value = trim($value);
                    $device->params()->create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }

        return $device;
    }
}
