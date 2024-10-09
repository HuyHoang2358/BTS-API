<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Models\Scan;
use Illuminate\Http\JsonResponse;

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
}
