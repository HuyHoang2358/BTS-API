<?php

namespace App\Http\Controllers\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\CreateCountryRequest;
use App\Http\Requests\Address\UpdateCountryRequest;
use App\Models\Address\Country;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Country::query();
        $countries = QueryHelper::applyQuery($query, $request, ['name','code','phone_code']);
        return ApiResponse::success($countries, ApiMessage::ADDRESS_COUNTRY_LIST);
    }

    public function store(CreateCountryRequest $request): JsonResponse
    {
        $country = Country::create($request->validated());
        return ApiResponse::success($country, ApiMessage::ADDRESS_COUNTRY_STORE_SUCCESS);
    }

    public function update($id, UpdateCountryRequest $request): JsonResponse
    {
        try {
            $country = Country::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "country_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_COUNTRY_NOT_FOUND, 404);
        }
        // get only value not null in request

        $country->update($request->validated());
        return ApiResponse::success($country, ApiMessage::ADDRESS_COUNTRY_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $country = Country::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "country_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_COUNTRY_NOT_FOUND, 404);
        }
        $country->delete();
        return ApiResponse::success($country, ApiMessage::ADDRESS_COUNTRY_DESTROY_SUCCESS);
    }
}

