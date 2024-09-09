<?php

namespace App\Http\Requests\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateCommuneRequest extends FormRequest
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
                'name' => $this->sanitizeInput($this->input('name','')),
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
        $communeId = $this->route('id');
        return [
            'name' => 'nullable|string|between:2,100',
            'code' => 'nullable|string|max:2|unique:communes,code,'.$communeId,
            'slug' => 'nullable',
            'district_id' => 'nullable|exists:districts,id|integer',
            'windy_area_id' => 'nullable|integer'
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'string' => __('customValidate.address.commune.name.string'),
                'between' => __('customValidate.address.commune.name.between'),
            ],
            'code' => [
                'string' => __('customValidate.address.commune.code.string'),
                'max' => __('customValidate.address.commune.code.max'),
                'unique' => __('customValidate.address.commune.code.unique'),
            ],
            'district_id' => [
                'exists' => __('customValidate.address.commune.country_id.exists'),
                'integer' => __('customValidate.address.commune.country_id.integer'),
            ],
            'windy_area_id' => [
                'integer' => __('customValidate.address.commune.windy_area_id.integer'),
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
