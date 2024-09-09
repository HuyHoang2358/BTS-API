<?php

namespace App\Http\Requests;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ImportExcelFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xlsx,xls',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'file' =>[
                'required' => __('customValidate.excel.file.required'),
                'mimes' => __('customValidate.excel.file.mimes'),
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
