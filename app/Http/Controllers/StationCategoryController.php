<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Requests\CreateStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\Address\Address;
use App\Models\Location;
use App\Models\Station;
use App\Models\StationCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class StationCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = StationCategory::query();
        $stations = QueryHelper::applyQuery(
            $query,
            $request,
            ['name', 'code'],
            ['location', 'address', 'address.country', 'address.province', 'address.district', 'address.commune'],
            ['location_id', 'address_id']
        );

        return ApiResponse::success($stations, ApiMessage::STATION_LIST);
    }
    public function store(CreateStationRequest $request): JsonResponse
    {
        // Create station
        $validated = $request->validated();

        $station = StationCategory::create(
            Arr::only($validated, ['name', 'code', 'description'])
        );
        $station->location_id = Location::create([
            'latitude' => $validated['location_latitude'],
            'longitude' => $validated['location_longitude'],
            'height' => $validated['location_height'],
        ])->id;
        $station->address_id = Address::create([
            'detail' => $validated['address_detail'] ?? null,
            'country_id' => $validated['address_country_id'],
            'province_id' => $validated['address_province_id'],
            'district_id' => $validated['address_district_id'],
            'commune_id' => $validated['address_commune_id'],
        ])->id;
        $station->save();

        return ApiResponse::success($station, ApiMessage::STATION_STORE_SUCCESS);
    }

    public function update($id, UpdateStationRequest $request): JsonResponse
    {
        $validate = $request->validated();
        $validate = array_filter($validate, function ($value) {
            return !is_null($value);
        });

        try {
            $station = StationCategory::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "station_category_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }

        $station->update($validate);
        $location = $station->location;
        $updateLocation = [];
        if (isset($validate['location_latitude'])) $updateLocation['latitude'] = $validate['location_latitude'];
        if (isset($validate['location_longitude'])) $updateLocation['longitude'] = $validate['location_longitude'];
        if (isset($validate['location_height'])) $updateLocation['height'] = $validate['location_height'];
        if (!empty($updateLocation)) $location->update($updateLocation);

        $address = $station->address;
        $updateAddress = [];
        if (isset($validate['address_detail'])) $updateAddress['detail'] = $validate['address_detail'];
        if (isset($validate['address_country_id'])) $updateAddress['country_id'] = $validate['address_country_id'];
        if (isset($validate['address_province_id'])) $updateAddress['province_id'] = $validate['address_province_id'];
        if (isset($validate['address_district_id'])) $updateAddress['district_id'] = $validate['address_district_id'];
        if (isset($validate['address_commune_id'])) $updateAddress['commune_id'] = $validate['address_commune_id'];
        if (!empty($updateAddress)) $address->update($updateAddress);

        return ApiResponse::success($station, ApiMessage::STATION_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $station = StationCategory::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "station_category_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::STATION_NOT_FOUND, 404);
        }
        $station->delete();
        return ApiResponse::success($station, ApiMessage::STATION_DESTROY_SUCCESS);
    }


}
