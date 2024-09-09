<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateProvinceRequest extends FormRequest
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
                'name' => $this->input('name',''),
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
        $provinceId = $this->route('id');
        return [
            'name' => 'nullable|string|alpha|between:2,100|unique:provinces,name,'.$provinceId,
            'code' => 'nullable|string|max:2|unique:provinces,code,'.$provinceId,
            'slug' => 'nullable',
            'country_id' => 'nullable|exists:countries,id|integer',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'string' => __('customValidate.address.province.name.string'),
                'between' => __('customValidate.address.province.name.between'),
                'unique' => __('customValidate.address.province.name.unique'),
                'alpha' => __('customValidate.address.province.name.alpha'),
            ],
            'code' => [
                'string' => __('customValidate.address.province.code.string'),
                'max' => __('customValidate.address.province.code.max'),
                'unique' => __('customValidate.address.province.code.unique'),
            ],
            'country_id' => [
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
