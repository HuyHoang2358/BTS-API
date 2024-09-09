<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateCountryRequest extends FormRequest
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
        if($this->has('phone_code')) {
            $this->merge([
                'phone_code' => $this->input('phone_code') ? '+' . ltrim($this->input('phone_code'), '+') : null,
            ]);
        };
        if ($this->has('language')) {
            $this->merge([
                'language' => strtolower($this->input('language', '')),
            ]);
        };
        if ($this->has('currency')) {
            $this->merge([
                'currency' => $this->input('currency', ''),
            ]);
        };
        if ($this->has('name')) {
            $this->merge([
                'name' => $this->input('name',''),
            ]);
        };
    }

    public function rules(): array
    {
        $countryId = $this->route('id');
        return [
            'name' => 'nullable|string|between:2,100|unique:countries,name,'.$countryId,
            'code' => 'nullable|string|max:10|unique:countries,code,'.$countryId,
            'phone_code' => 'nullable|string|between:2,4|unique:countries,phone_code,'.$countryId,
            'currency' => 'nullable|string',
            'language' => 'nullable|alpha|between:2,2',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'string' => __('customValidate.address.country.name.string'),
                'between' => __('customValidate.address.country.name.between'),
                'unique' => __('customValidate.address.country.name.unique'),
            ],
            'code' => [
                'string' => __('customValidate.address.country.code.string'),
                'max' => __('customValidate.address.country.code.max'),
                'unique' => __('customValidate.address.country.code.unique'),
            ],
            'phone_code' => [
                'string' => __('customValidate.address.country.phone_code.string'),
                'between' => __('customValidate.address.country.phone_code.between'),
                'unique' => __('customValidate.address.country.phone_code.unique'),
            ],
            'currency' => [
                'string' => __('customValidate.address.country.currency.string'),
            ],
            'language' => [
                'between' => __('customValidate.address.country.language.between'),
                'alpha' => __('customValidate.address.country.language.alpha'),
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
