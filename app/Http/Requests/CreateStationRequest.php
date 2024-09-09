<?php

namespace App\Http\Requests;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateStationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:500',
            'code' => 'required|string|max:100|unique:stations,code',
            'description' => 'nullable|string|max:1000',

            'location_latitude' => 'required|numeric|between:-90,90',
            'location_longitude' => 'required|numeric|between:-180,180',
            'location_height' => 'required|numeric|max:1000',

            'address_detail' => 'nullable|string|max:500',
            'address_country_id' => 'required|integer|exists:countries,id',
            'address_province_id' => 'required|integer|exists:provinces,id',
            'address_district_id' => 'required|integer|exists:districts,id',
            'address_commune_id' => 'required|integer|exists:communes,id',
        ];
    }
    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.station.name.required'),
                'max' => __('customValidate.pole.station.name.max'),
            ],
            'code' => [
                'required' => __('customValidate.station.code.required'),
                'string' => __('customValidate.station.code.string'),
                'max' => __('customValidate.station.code.max'),
                'unique' => __('customValidate.station.code.unique'),
            ],
            'description' => [
                'string' => __('customValidate.station.description.string'),
                'max' => __('customValidate.station.description.max'),
            ],
            'location_latitude' => [
                'required' => __('customValidate.station.location_latitude.required'),
                'numeric' => __('customValidate.station.location_latitude.numeric'),
                'between' => __('customValidate.station.location_latitude.between'),
            ],
            'location_longitude' => [
                'required' => __('customValidate.station.location_longitude.required'),
                'numeric' => __('customValidate.station.location_longitude.numeric'),
                'between' => __('customValidate.station.location_longitude.between'),
            ],
            'location_height' => [
                'required' => __('customValidate.station.location_height.required'),
                'numeric' => __('customValidate.station.location_height.numeric'),
                'max' => __('customValidate.station.location_height.max'),
            ],
            'address_detail' => [
                'string' => __('customValidate.station.address_detail.string'),
                'max' => __('customValidate.station.address_detail.max'),
            ],
            'address_country_id' => [
                'required' => __('customValidate.station.address_country_id.required'),
                'exists' => __('customValidate.station.address_country_id.exists'),
                'integer' => __('customValidate.station.address_country_id.integer'),
            ],
            'address_province_id' => [
                'required' => __('customValidate.station.address_province_id.required'),
                'exists' => __('customValidate.station.address_province_id.exists'),
                'integer' => __('customValidate.station.address_province_id.integer'),
            ],
            'address_district_id' => [
                'required' => __('customValidate.station.address_district_id.required'),
                'exists' => __('customValidate.station.address_district_id.exists'),
                'integer' => __('customValidate.station.address_district_id.integer'),
            ],
            'address_commune_id' => [
                'required' => __('customValidate.station.address_commune_id.required'),
                'exists' => __('customValidate.station.address_commune_id.exists'),
                'integer' => __('customValidate.station.address_commune_id.integer'),
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
