<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // TODO: Xóa ký tự đặc biệt
    protected function sanitizeInput(?string $input): ?string
    {
        return $input ? preg_replace('/[^a-zA-Z0-9_]/', '', $input) : null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizeInput($this->input('name','')),
            'code' => $this->sanitizeInput($this->input('code', '')),
            'slug' => Str::slug($this->input('name', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|between:2,100',
            'code' => 'required|string|max:3|unique:districts,code',
            'slug' => 'nullable',
            'province_id' => 'required|exists:provinces,id|integer',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.address.district.name.required'),
                'string' => __('customValidate.address.district.name.string'),
                'between' => __('customValidate.address.district.name.between'),
            ],
            'code' => [
                'required' => __('customValidate.address.district.code.required'),
                'string' => __('customValidate.address.district.code.string'),
                'max' => __('customValidate.address.district.code.max'),
                'unique' => __('customValidate.address.district.code.unique'),
            ],
            'province_id' => [
                'required' => __('customValidate.address.district.province_id.required'),
                'exists' => __('customValidate.address.district.province_id.exists'),
                'integer' => __('customValidate.address.district.province_id.integer'),
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
