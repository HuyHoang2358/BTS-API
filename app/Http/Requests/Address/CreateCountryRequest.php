<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property mixed $phone_code
 */
class CreateCountryRequest extends FormRequest
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
            'currency' => $this->sanitizeInput($this->input('currency', '')),
            'phone_code' => $this->input('phone_code') ? '+' . ltrim($this->input('phone_code'), '+') : null,
            'language' => strtolower($this->input('language', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|between:2,100|unique:countries',
            'code' => 'required|string|max:10|unique:countries',
            'phone_code' => 'required|string|between:2,4|unique:countries',
            'currency' => 'string',
            'language' => 'required|alpha|between:2,2',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.address.country.name.required'),
                'string' => __('customValidate.address.country.name.string'),
                'between' => __('customValidate.address.country.name.between'),
                'unique' => __('customValidate.address.country.name.unique'),
            ],
            'code' => [
                'required' => __('customValidate.address.country.code.required'),
                'string' => __('customValidate.address.country.code.string'),
                'max' => __('customValidate.address.country.code.max'),
                'unique' => __('customValidate.address.country.code.unique'),
            ],
            'phone_code' => [
                'required' => __('customValidate.address.country.phone_code.required'),
                'string' => __('customValidate.address.country.phone_code.string'),
                'between' => __('customValidate.address.country.phone_code.between'),
                'unique' => __('customValidate.address.country.phone_code.unique'),
            ],
            'currency' => [
                'string' => __('customValidate.address.country.currency.string'),
            ],
            'language' => [
                'required' => __('customValidate.address.country.language.required'),
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
