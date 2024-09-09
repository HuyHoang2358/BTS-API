<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateDistrictRequest extends FormRequest
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
        if ($this->has('name')) {
            $this->merge([
                'slug' => Str::slug($this->input('name', '')),
            ]);
        };
        if ($this->has('code')) {
            $this->merge([
                'code' => $this->sanitizeInput($this->input('code', '')),
            ]);
        };
    }

    public function rules(): array
    {
        $districtId = $this->route('id');
        return [
            'name' => 'nullable|string|between:2,100',
            'code' => 'nullable|string|max:2|unique:districts,code,'.$districtId,
            'slug' => 'nullable',
            'province_id' => 'nullable|exists:provinces,id|integer',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'string' => __('customValidate.address.district.name.string'),
                'between' => __('customValidate.address.district.name.between'),
            ],
            'code' => [
                'string' => __('customValidate.address.district.code.string'),
                'max' => __('customValidate.address.district.code.max'),
                'unique' => __('customValidate.address.district.code.unique'),
            ],
            'province_id' => [
                'exists' => __('customValidate.address.district.country_id.exists'),
                'integer' => __('customValidate.address.district.country_id.integer'),
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
