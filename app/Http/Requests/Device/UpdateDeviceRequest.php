<?php

namespace App\Http\Requests\Device;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->input('name', '')),
        ]);
    }

    public function rules(): array
    {
        $deviceId = $this->route('id');
        return [
            'name' => 'nullable|max:500|unique:devices,name,' . $deviceId,
            'slug' => 'nullable',
            'description' => 'nullable|max:1000',
            'images' => 'nullable',
            'model_url' => 'nullable|max:2048',
            'length' => 'nullable|numeric|between:0,1000000',
            'width' => 'nullable|numeric|between:0,1000000',
            'depth' => 'nullable|numeric|between:0,1000000',
            'weight' => 'nullable|numeric|between:0,1000000',
            'diameter' => 'nullable|numeric|between:0,1000000',
            'device_category_id' => 'nullable|exists:device_categories,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'params' => 'nullable|array',
            /*'params' => 'nullable|string|max:1000|regex:/^(\s*\w+\s*:\s*\w+)(\s*,\s*\w+\s*:\s*\w+)*$/',*/
        ];
    }


    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'max' => __('customValidate.device.name.max'),
                'unique' => __('customValidate.device.name.unique'),
            ],
            'description' => [
                'max' => __('customValidate.device.description.max'),
            ],
            'model_url' => [
                'max' => __('customValidate.device.model_url.max'),
            ],
            'length' => [
                'numeric' => __('customValidate.device.length.numeric'),
                'between' => __('customValidate.device.length.between'),
            ],
            'width' => [
                'numeric' => __('customValidate.device.width.numeric'),
                'between' => __('customValidate.device.width.between'),
            ],
            'depth' => [
                'numeric' => __('customValidate.device.depth.numeric'),
                'between' => __('customValidate.device.depth.between'),
            ],
            'weight' => [
                'numeric' => __('customValidate.device.weight.numeric'),
                'between' => __('customValidate.device.weight.between'),
            ],
            'diameter' => [
                'numeric' => __('customValidate.device.diameter.numeric'),
                'between' => __('customValidate.device.diameter.between'),
            ],
            'device_category_id' => [
                'exists' => __('customValidate.device.device_category_id.exists'),
            ],
            'vendor_id' => [
                'exists' => __('customValidate.device.vendor_id.exists'),
            ],
            'params' => [
                'string' => __('customValidate.device.params.string'),
                'max' => __('customValidate.device.params.max'),
                'regex' => __('customValidate.device.params.regex'),
            ]

        ];
    }

    // fail validate
    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::error($validator->errors(), ApiMessage::VALIDATE_FAIL, 422);
        throw new ValidationException($validator, $response);
    }
}
