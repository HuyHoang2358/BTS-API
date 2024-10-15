<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Models\Measurement;
use App\Models\Pole\Pole;
use App\Models\Pole\PoleDevice;
use App\Models\Pole\PoleParam;
use App\Models\Scan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SebastianBergmann\Diff\Exception;

class ScanController extends Controller
{

    public function detail($id): JsonResponse
    {
        $scan = Scan::with([
            'models', 'station', 'station.address',
            'poles', 'poles.category', 'poles.poleDevices', 'poles.poleParam',
            'poles.poleDevices.deviceInfo' , 'poles.poleDevices.deviceInfo.vendor',
            'poles.poleDevices.deviceInfo.category', 'poles.poleDevices.deviceInfo.params',
            'poles.poleDevices.geometryBox',
            ])->find($id);

        if (!$scan) {
            return ApiResponse::error(null, ApiMessage::STATION_NOT_FOUND, 404);
        }
        return ApiResponse::success($scan, ApiMessage::STATION_DETAIL);
    }

    public function images($id): JsonResponse
    {
        try {
            $images = Scan::with([ 'images', 'images.gps', 'images.cameraPose', 'images.cameraPose.geometryCone', 'images.gimbal',])->find($id)->images;
            return ApiResponse::success($images, ApiMessage::STATION_DETAIL);
        }catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), ApiMessage::STATION_NOT_FOUND, 404);
        }

    }

    public function measurements($id): JsonResponse
    {
       $measurements = Measurement::where('scan_id', $id)->where('is_active', 1)->get();
       return ApiResponse::success($measurements, ApiMessage::STATION_MEASUREMENT_INDEX);

    }

    public function storeMeasurement(Request $request, $id): JsonResponse
    {
        $input = $request->all();
        $measurements = json_decode($input['measurements']);
        $oldMeasurements = Measurement::where('scan_id', $id)->where('is_active', 1)->get();
        if ($oldMeasurements->count() > 0) {
            foreach ($oldMeasurements as $item) {
                if (count($measurements) > 0) $item->update(['is_active' => 0]);
                else $item->delete();
            }
        }

        $measurement = null;
        if (count($measurements) > 0){
            $measurement = Measurement::create([
                'scan_id' => $id,
                'measurements' => $input['measurements'],
                'user_id' => auth()->id(),
                'is_active' => 1
            ]);
        }

        return ApiResponse::success($measurement, ApiMessage::STATION_MEASUREMENT_ADD);
    }

    public function updateMeasurement($id, $measurement_id, Request $request){

    }

    public function historyPole($id, $pole_id): JsonResponse
    {
        $poleParams = PoleParam::where('pole_id', $pole_id)->where('is_active', '0')->orderBy('updated_at', 'desc')->get();

        return  ApiResponse::success($poleParams, ApiMessage::POLE_STRESS_SUCCESS);

    }
    public function updatePoleParams($id, $pole_id, Request $request): JsonResponse
    {
        $activePoleParam = PoleParam::where('pole_id', $pole_id)->where('is_active',1)->first();
        $activePoleParam->update(['is_active' => 0]);

        // Create new pole param
        $input = $request->all();
        $poleParam = PoleParam::create([
            'pole_id' => $pole_id,
            'height' => $input['height'] ?? $activePoleParam->height,
            'is_roof' => $input['is_roof'] ?? $activePoleParam->is_roof,
            'house_height' => $input['house_height'] ?? $activePoleParam->house_height,
            'diameter_bottom_tube' => $input['diameter_bottom_tube'] ?? $activePoleParam->diameter_bottom_tube,
            'diameter_top_tube' => $input['diameter_top_tube'] ?? $activePoleParam->diameter_top_tube,
            'diameter_strut_tube' => $input['diameter_strut_tube'] ?? $activePoleParam->diameter_strut_tube,
            'diameter_body_tube' => $input['diameter_body_tube'] ?? $activePoleParam->diameter_body_tube,
            'tilt_angle' => $input['tilt_angle'] ?? $activePoleParam->tilt_angle,
            'is_shielded' => $input['is_shielded'] ?? $activePoleParam->is_shielded,
            'size' => $input['size'] ?? $activePoleParam->size,
            'top_size'=> $input['top_size'] ?? $activePoleParam->top_size,
            'foot_size' => $input['foot_size'] ?? $activePoleParam->foot_size,
            'description' => $input['description'] ?? $activePoleParam->description,
            'user_id' => auth()->id(),
            'is_active' => 1
        ]);

        return ApiResponse::success($poleParam, ApiMessage::STATION_SCAN_POLE_PARAM);

    }
    public function rollbackPoleParam($id, $pole_id,Request $request): JsonResponse
    {
        $input = $request->all();
        try{
            $rollbackPoleParam = PoleParam::findOrfail($input['pole_param_id']);
            $activePoleParam = PoleParam::where('pole_id', $pole_id)->where('is_active',1)->first();
        }catch (Exception $e){
            $errors = [
                "rollback_pole_param_id" => $input['pole_param_id'],
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        $rollbackPoleParam->update(['is_active' => 1]);
        $activePoleParam->update(['is_active' => 0]);
        return ApiResponse::success($rollbackPoleParam, ApiMessage::STATION_SCAN_POLE_PARAM);
    }

    public function historyDevice($id, $pole_id, $device_index): JsonResponse
    {
        $poleDevices = PoleDevice::where('pole_id', $pole_id)->where('index', $device_index)->where('is_active', 0)->orderBy('updated_at', 'desc')->get();
        return  ApiResponse::success($poleDevices, ApiMessage::POLE_STRESS_SUCCESS);
    }
    public function updateDeviceParam($id, $pole_id, $device_index, Request $request): JsonResponse
    {
        $activeDeviceParam = Poledevice::where('pole_id', $pole_id)->where('index', $device_index)->where('is_active',1)->first();
        $activeDeviceParam->update(['is_active' => 0]);

        // Create new pole param
        $input = $request->all();
        $poleDevice = PoleDevice::create([
            'pole_id' => $pole_id,
            'index' => $device_index,
            'device_id' => $input['device_id'] ?? $activeDeviceParam->device_id,
            'geometry_box_id' => $input['geometry_box_id'] ?? $activeDeviceParam->geometry_box_id,
            'rotation' => $input['rotation'] ?? $activeDeviceParam->rotation,
            'translation' => $input['translation'] ?? $activeDeviceParam->translation,
            'vertices' => $input['vertices'] ?? $activeDeviceParam->vertices,
            'tilt'=> $input['tilt'] ?? $activeDeviceParam->tilt,
            'azimuth' => $input['azimuth'] ?? $activeDeviceParam->azimuth,
            'height' => $input['height'] ?? $activeDeviceParam->height,
            'ai_device_width' => $input['ai_device_width'] ?? $activeDeviceParam->ai_device_width,
            'ai_device_height' => $input['ai_device_height'] ?? $activeDeviceParam->ai_device_height,
            'ai_device_depth' => $input['ai_device_depth'] ?? $activeDeviceParam->ai_device_depth,
            'suggested_image' => $input['suggested_image'] ?? $activeDeviceParam->suggested_image,
            'user_id'  => auth()->id(),
            'is_active' => 1,
            'description' => $input['description'] ?? $activeDeviceParam->description,
        ]);

        return ApiResponse::success($poleDevice, ApiMessage::STATION_SCAN_POLE_PARAM);

    }
    public function rollbackDeviceParam($id, $pole_id, $device_index, Request $request): JsonResponse
    {
        $input = $request->all();
        try{
            $rollbackPoleDevice = PoleDevice::findOrfail($input['pole_device_id']);
            $activePoleDevice = PoleDevice::where('pole_id', $pole_id)->where('index',$device_index)->where('is_active',1)->first();
        }catch (Exception $e){
            $errors = [
                "rollback_pole_device_id" => $input['pole_device_id'],
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }

        $rollbackPoleDevice->update(['is_active' => 1]);
        $activePoleDevice->update(['is_active' => 0]);
        return ApiResponse::success($rollbackPoleDevice, ApiMessage::STATION_SCAN_POLE_PARAM);

    }
}
