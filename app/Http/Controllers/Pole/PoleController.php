<?php

namespace App\Http\Controllers\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pole\CreatePoleRequest;
use App\Http\Requests\Pole\UpdatePoleRequest;
use App\Models\Device\Device;
use App\Models\Pole\Pole;
use App\Models\Pole\PoleDevice;
use App\Models\Pole\PoleParam;
use App\Models\Station;
use App\Models\StationPole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Pole::query();
        $poles = QueryHelper::applyQuery($query, $request, ['name'], ['category','params', 'devices'], ['pole_category_id']);

        return ApiResponse::success($poles, ApiMessage::POLE_LIST);
    }

    public function store(CreatePoleRequest $request): JsonResponse
    {

        $pole = Pole::create($request->validated());
        if($request->has('params')){
            $params =$request->validated('params');
            foreach ($params as $param){
               PoleParam::create([
                    'pole_id' => $pole->id,
                    'key' => $param['key'],
                    'value' => $param['value']
                ]);
            }
        }
        if ($request->has('station_code')) {
            StationPole::create([
                'pole_id' => $pole->id,
                'station_id' => Station::where('code', $request->validated('station_code'))->first()->id,
            ]);
        }
        return ApiResponse::success($pole->with('params'), ApiMessage::POLE_STORE_SUCCESS);
    }

    public function update($id, UpdatePoleRequest $request): JsonResponse
    {
        try {
            $pole = Pole::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "pole_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        // get only value not null in request

        $pole->update($request->validated());
        $pole->params()->delete();
        if($request->has('params')){
            $params =$request->validated('params');
            foreach ($params as $param){
                PoleParam::create([
                    'pole_id' => $pole->id,
                    'key' => $param['key'],
                    'value' => $param['value']
                ]);
            }
        }
        if ($request->has('station_code')) {
            $stationPole = StationPole::where('pole_id', $pole->id)->first();
            if (!$stationPole) {
                StationPole::create([
                    'pole_id' => $pole->id,
                    'station_id' => Station::where('code', $request->validated('station_code'))->first()->id,
                ]);
            } else
            $stationPole->update([
                'station_id' =>Station::where('code', $request->validated('station_code'))->first()->id,
            ]);
        }


        return ApiResponse::success($pole, ApiMessage::POLE_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $pole = Pole::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_category_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        $pole->delete();
        return ApiResponse::success($pole, ApiMessage::POLE_DESTROY_SUCCESS);
    }

    public function addDevice(Request $request): JsonResponse
    {
        $input = $request->all();

        try {
            $pole = Pole::findOrFail($input['pole_id']);
            $device = Device::findOrFail($input['device_id']);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_id" => $input['device_id'],
                "pole_id" => $input['pole_id'],
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        $attached_at = null;
        if ($input['attached_at'])
            $attached_at = \Carbon\Carbon::createFromTimestamp($input['attached_at'])->toDateTimeString();

        $poleDevice = PoleDevice::create([
            'pole_id' => $pole->id,
            'device_id' => $input['device_id'],
            'attached_at' =>  $attached_at,
            'x' => $input['x'],
            'y' => $input['y'],
            'z' => $input['z'],
            'alpha' => $input['alpha'],
            'beta' => $input['beta'],
            'gama' => $input['gama'],
        ]);

        return ApiResponse::success($poleDevice, ApiMessage::POLE_DEVICE_STORE_SUCCESS);
    }

    public function updateDevice($id, Request $request): JsonResponse
    {
        $input = $request->all();

        try {
            $poleDevice = PoleDevice::find($id);
            $pole = Pole::findOrFail($input['pole_id']);
            $device = Device::findOrFail($input['device_id']);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_id" => $input['device_id'],
                "pole_id" => $input['pole_id'],
                "pole_device_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }

        $attached_at = null;
        if ($input['attached_at'])
            $attached_at = \Carbon\Carbon::createFromTimestamp($input['attached_at'])->toDateTimeString();

        $poleDevice->update([
            'pole_id' => $input['pole_id'],
            'device_id' => $input['device_id'],
            'attached_at' =>  $attached_at,
            'x' => $input['x'],
            'y' => $input['y'],
            'z' => $input['z'],
            'alpha' => $input['alpha'],
            'beta' => $input['beta'],
            'gama' => $input['gama'],
        ]);

        return ApiResponse::success($poleDevice, ApiMessage::POLE_DEVICE_STORE_SUCCESS);
    }

    public function removeDevice($id): JsonResponse
    {
        try {
            $poleDevice = PoleDevice::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "pole_device_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        $poleDevice->delete();
        return ApiResponse::success($poleDevice, ApiMessage::POLE_DEVICE_REMOVE_SUCCESS);
    }


}
