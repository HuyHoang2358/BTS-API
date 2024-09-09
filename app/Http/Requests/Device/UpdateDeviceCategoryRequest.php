<?php

namespace App\Http\Requests\Device;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateDeviceCategoryRequest extends FormRequest
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
        $deviceCategoryId = $this->route('id');
        return [
            'name' => 'nullable|max:255|unique:device_categories,name,' . $deviceCategoryId,
            'slug' => 'nullable',
            'description' => 'nullable|max:500'
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'max' => __('customValidate.device.category.name.max'),
                'unique' => __('customValidate.device.category.name.unique'),
            ],
            'description' => [
                'max' => __('customValidate.device.category.description.max'),
            ],
        ];
    }

    // fail validate
    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::error($validator->errors(), ApiMessage::VALIDATE_FAIL, 422);
        throw new ValidationException($validator, $response);
    }
}
