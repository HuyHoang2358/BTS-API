<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Requests\CreateStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Models\Address\Address;
use App\Models\Location;
use App\Models\model3D;
use App\Models\Pole\Pole;
use App\Models\Station;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use function Symfony\Component\String\s;

class StationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Station::query();
        $stations = QueryHelper::applyQuery(
            $query,
            $request,
            ['name', 'code'],
            ['location', 'address', 'address.country', 'address.province', 'address.district', 'address.commune', 'poles'],
            ['location_id', 'address_id']
        );

        return ApiResponse::success($stations, ApiMessage::STATION_LIST);
    }

    public function detail($id): JsonResponse
    {
        try {
            $station = Station::with([
                'location', 'address', 'address.country',
                'address.province', 'address.district',
                'address.commune', 'poles', 'poles.devices', 'poles.devices.category', 'poles.devices.vendor'
            ])->findOrFail($id)->makeHidden([
                'location_id', 'address_id'
            ]);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "station_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::STATION_NOT_FOUND, 404);
        }

        return ApiResponse::success($station, ApiMessage::STATION_LIST);
    }

    public function listCode(): JsonResponse
    {
        $stations = Station::all()->select('id','code');
        return ApiResponse::success($stations, ApiMessage::STATION_LIST);
    }
    public function store(CreateStationRequest $request): JsonResponse
    {
        // Create station
        $validated = $request->validated();

        $station = Station::create(
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
        // remove null value in validate

        $validate = array_filter($validate, function ($value) {
            return !is_null($value);
        });

        //return ApiResponse::success($validate, ApiMessage::STATION_STORE_SUCCESS);
        try {
            $station = Station::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "pole_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }

        $station->update($validate);
        $location = $station->location;
        if (isset($validate['location_latitude'])) {
            $location->update([
                'latitude' => $validate['location_latitude'],
            ]);
        }
        if (isset($validate['location_longitude'])) {
            $location->update([
                'longitude' => $validate['location_longitude'],
            ]);
        }
        if (isset($validate['location_height'])) {
            $location->update([
                'height' => $validate['location_height'],
            ]);
        }
        $address = $station->address;
        if (isset($validate['address_detail'])) {
            $address->update([
                'detail' => $validate['address_detail'],
            ]);
        }
        if (isset($validate['address_country_id'])) {
            $address->update([
                'country_id' => $validate['address_country_id'],
            ]);
        }
        if (isset($validate['address_province_id'])) {
            $address->update([
                'province_id' => $validate['address_province_id'],
            ]);
        }
        if (isset($validate['address_district_id'])) {
            $address->update([
                'district_id' => $validate['address_district_id'],
            ]);
        }
        if (isset($validate['address_commune_id'])) {
            $address->update([
                'commune_id' => $validate['address_commune_id'],
            ]);
        }
        return ApiResponse::success($station, ApiMessage::STATION_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $station = Station::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "station_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::STATION_NOT_FOUND, 404);
        }
        $station->delete();
        return ApiResponse::success($station, ApiMessage::STATION_DESTROY_SUCCESS);
    }

    public function addPole($id, Request $request): JsonResponse
    {
        $input = $request->all();
        try {
            $station = Station::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "station_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::STATION_NOT_FOUND, 404);
        }

        try {
            $pole = Station::findOrFail($input['pole_id']);
        } catch (ModelNotFoundException $e) {
            $errors = [
                'pole_id' => $input['pole_id'],
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }

        $station->poles()->attach($input['pole_id'], $input);
        return ApiResponse::success($station, ApiMessage::STATION_POLE_STORE_SUCCESS);
    }

    public function removePole($id, $pole_id): JsonResponse
    {
        try {
            $station = Station::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "station_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::STATION_NOT_FOUND, 404);
        }

        try {
            $pole = Station::findOrFail($pole_id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                'pole_id' => $pole_id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }

        $station->poles()->detach($pole_id);
        return ApiResponse::success($station, ApiMessage::STATION_POLE_REMOVE_SUCCESS);
    }


    public function updateData($id){
        $station = Station::findOrFail($id);
        return ApiResponse::success($station, ApiMessage::STATION_UPDATE_SUCCESS);
    }

}
