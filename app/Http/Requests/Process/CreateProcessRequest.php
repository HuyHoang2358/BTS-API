<?php

namespace App\Http\Requests\Process;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'station_code' => 'required|exists:stations,code',
            'date' => 'required|date',
        ];
    }
    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'station_code' =>[
                'required' => __('customValidate.process.station_code.required'),
                'exists' => __('customValidate.process.station_code.exists'),
            ],
            'date' => [
                'required' => __('customValidate.process.date.required'),
                'date' => __('customValidate.process.date.date'),
            ],
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::error($validator->errors(), ApiMessage::VALIDATE_FAIL, 422);
        throw new ValidationException($validator, $response);
    }

}
