<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateProvinceRequest extends FormRequest
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
            'name' => 'required|string|alpha|between:2,100|unique:provinces,name',
            'code' => 'required|string|max:2|unique:provinces,code',
            'slug' => 'nullable',
            'country_id' => 'required|exists:countries,id|integer',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.address.province.name.required'),
                'string' => __('customValidate.address.province.name.string'),
                'between' => __('customValidate.address.province.name.between'),
                'unique' => __('customValidate.address.province.name.unique'),
                'alpha' => __('customValidate.address.province.name.alpha'),
            ],
            'code' => [
                'required' => __('customValidate.address.province.code.required'),
                'string' => __('customValidate.address.province.code.string'),
                'max' => __('customValidate.address.province.code.max'),
                'unique' => __('customValidate.address.province.code.unique'),
            ],
            'country_id' => [
                'required' => __('customValidate.address.province.country_id.required'),
                'exists' => __('customValidate.address.province.country_id.exists'),
                'integer' => __('customValidate.address.province.country_id.integer'),
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
