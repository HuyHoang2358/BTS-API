<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Models\Measurement;
use App\Models\Pole\PoleParam;
use App\Models\Scan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanController extends Controller
{

    public function detail($id): JsonResponse
    {
        $scan = Scan::with([
            'models',
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
       $measurements = Measurement::where('scan_id', $id)->get();
       return ApiResponse::success($measurements, ApiMessage::STATION_MEASUREMENT_INDEX);

    }

    public function storeMeasurement(Request $request, $id): JsonResponse
    {
        $oleMeasurements = Measurement::where('scan_id', $id)->where('is_active', 1)->get();
        if ($oleMeasurements->count() > 0) {
            foreach ($oleMeasurements as $measurement) {
                $measurement->update(['is_active' => 0]);
            }
        }

        $input = $request->all();
        $measurement = Measurement::create([
            'scan_id' => $id,
            'measurements' => $input['measurements'],
            'user_id' => auth()->id(),
            'is_active' => 1
        ]);

        return ApiResponse::success($measurement, ApiMessage::STATION_MEASUREMENT_ADD);
    }

    public function updateMeasurement($id, $measurement_id, Request $request){

    }

    public function historyPole($id, $pole_id){
        $poleParams = PoleParam::where('pole_id', $pole_id)->orderBy('updated_at', 'desc')->get();

        return  ApiResponse::success($poleParams, ApiMessage::POLE_STRESS_SUCCESS);

    }
    public function updatePoleParams($id, $pole_id, Request $request){
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

    }


}
