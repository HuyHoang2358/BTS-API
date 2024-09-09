<?php

namespace App\Http\Requests\Device;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateVendorRequest extends FormRequest
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
        return [
            'name' => 'required|max:255|unique:vendors,name',
            'slug' => 'nullable',
            'description' => 'nullable|max:500',
            'website' => 'nullable|url|max:2048',
            'logo' => 'nullable|string|max:500',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.device.vendor.name.required'),
                'max' => __('customValidate.device.vendor.name.max'),
                'unique' => __('customValidate.device.vendor.name.unique'),
            ],
            'description' => [
                'max' => __('customValidate.device.vendor.description.max'),
            ],
            'website' => [
                'url' => __('customValidate.device.vendor.website.url'),
                'max' => __('customValidate.device.vendor.website.max'),
            ],
            'logo' => [
                'string' => __('customValidate.device.vendor.logo.string'),
                'max' => __('customValidate.device.vendor.logo.max'),
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
