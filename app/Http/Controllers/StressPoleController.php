<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Device\Device;
use App\Models\Device\DeviceCategory;
use App\Models\Pole\Pole;
use App\Models\Pole\PoleDevice;
use App\Models\Station;
use App\Models\WindyArea;
use Illuminate\Http\Request;

class StressPoleController extends Controller
{
    protected function prepareStationData($station_code): array
    {
        $station = Station::where('code', $station_code)->with(['address','address.commune'])->first();
        if(!$station) return [];

        $data["windy_area"] = WindyArea::findOrFail($station->address->commune->windy_area_id)->name;
        $data["station_code"] = $station_code;
        $data["poles"] = [];
        foreach($station->poles as $pole) $data["poles"][] = $this->preparePoleData($pole);
        return $data;
    }
    protected function calHeightOfDevice($x, $y, $z, $center_x = 0, $center_y = 0, $center_z = 0)
    {
        if (!$z || !$center_z) return 0;
        return $z - $center_z;
    }
    protected function processingDeviceInformation($device): array
    {
        $data["id"] = $device->id;
        $data["depth"] = $device->depth ? $device->depth/ 1000 : 0;
        $data["width"] = $device->width ? $device->width/ 1000 : 0;
        $data["height"] = $device->height ? $device->height/ 1000 : 0;
        $data["weight"] = $device->weight ?? 0;
        $data["DC"] = $this->calHeightOfDevice($device->pivot->x, $device->pivot->y, $device->pivot->z);
        $data['quantity'] = 1;
        return $data;
    }
    protected function splitAndProcessingDevices($devices): array
    {
        $deviceCategories = DeviceCategory::all();
        $data = [];
        foreach($deviceCategories as $deviceCategory){
            $data[$deviceCategory->name] = [];
        }
        foreach($devices as $device){
            $data[$device->category->name][] = $this->processingDeviceInformation($device);
        }
        return $data;
    }
    protected function preparePoleData($pole): array
    {
        // get all devices of pole
        $devices = $pole->devices;
        $data["devices"] = $this->splitAndProcessingDevices($devices);
        return $data;

        // split devices by category






        $pole = Pole::findOrFail($pole_id)->with('category')->first();
        if(!$pole) return [];
        $data["pole_height"] = $pole->height;
        $data["pole_is_roof"] = $pole->is_roof ? "TM" : "DD";
        $data["pole_house_height"] = $pole->house_height;
        $data["pole_category"] = $pole->category->code;
        $data['pole_size'] = $pole->size;
        $data['pole_diameter_body_tube'] = $pole->diameter_body_tube;
        if (str_contains($data["pole_category"], 'TD'))
            $data["pole_category"] = "TD";
        $data["devices"] = [];
        $data["devices"]["antenna"] = [
            "T1" => [
                (object)[
                    "device_depth" => 0.3,
                    "device_width" => 0.3,
                    "device_height" => 0.3,
                    "device_weight" => 0.3,
                    "device_dc" => 20,
                    "quantity" => 2
                ],
                (object)[
                    "device_depth" => 0.3,
                    "device_width" => 0.3,
                    "device_height" => 0.3,
                    "device_weight" => 0.3,
                    "device_dc" => 20,
                    "quantity" => 3
                ]
            ],
            "T2" => [
                (object)[
                    "device_depth" => 0.3,
                    "device_width" => 0.3,
                    "device_height" => 0.3,
                    "device_weight" => 0.3,
                    "device_dc" => 20,
                    "quantity" => 2
                ],
            ],
            "T3" => [],
            "T4" => [],
            "T5" => [],
        ];

        $data["devices"]["rru"] = [
            (object)[
                "device_depth" => 0.3,
                "device_width" => 0.3,
                "device_height" => 0.3,
                "device_weight" => 0.3,
                "device_dc" => 48,
                "quantity" => 1
            ],
            (object)[
                "device_depth" => 0.3,
                "device_width" => 0.3,
                "device_height" => 0.3,
                "device_weight" => 0.3,
                "device_dc" => 50,
                "quantity" => 1
            ],
            (object)[
                "device_depth" => 0.3,
                "device_width" => 0.3,
                "device_height" => 0.3,
                "device_weight" => 0.3,
                "device_dc" => 20,
                "quantity" => 1
            ],
        ];
        $data["devices"]["viba"] = [
            (object)[
                "device_depth" => 0.3,
                "device_width" => 0.3,
                "device_height" => 0.3,
                "device_weight" => 0.3,
                "device_dc" => 20,
                "quantity" => 1
            ],
            (object)[
                "device_depth" => 0.3,
                "device_width" => 0.3,
                "device_height" => 0.3,
                "device_weight" => 0.3,
                "device_dc" => 20,
                "quantity" => 1
            ],
        ];

       /* $poleDevices = PoleDevice::where('pole_id', $pole_id)->get();

        $deviceData = [];
        foreach ($poleDevices as $poleDevice){
            $ans = $this->prepareDeviceData($poleDevice->device_id);
            $ans["device_dc"] = $poleDevice->z ?? 20;
            $ans["quantity"] = 1;
            $deviceData[] = $ans;
        }
        //  split by antenna and rru, viba
        $data["devices"]["antenna"] = [];
        $data["devices"]["rru"] = [];
        $data["devices"]["viba"] = [];
        foreach($deviceData as $device){
            if($device["device_category"] == "Antenna") {
                // gộp các device cùng id lại làm 1

                $data["devices"]["antenna"][] = $device;
            }
            if($device["device_category"] == "RRU") $data["devices"]["rru"][] = $device;
            if($device["device_category"] == "Viba") $data["devices"]["viba"][] = $device;
        }*/

        return $data;
    }


    public function poleStress(): \Illuminate\Http\JsonResponse
    {
        $station_code = 'HAN-0212';
        $data = $this->prepareStationData($station_code);

        return ApiResponse::success($data);
    }
}
