<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Http\Requests\Pole\PoleStressRequest;
use App\Models\Device\Device;
use App\Models\Device\DeviceCategory;
use App\Models\Pole\Pole;
use App\Models\Station;
use App\Models\StationPole;
use App\Models\WindyArea;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StressPoleController extends Controller
{
    protected function prepareStationData($pole_id, $devices): array
    {
        $pole = Pole::find($pole_id);
        $data['pole'] = $pole;
        $scan = $pole->scan;
        $station = $scan->station;

        $data["windy_area"] = WindyArea::findOrFail($station->address->commune->windy_area_id)->name;
        $data["station_code"] = $station->code;
        $data["poles"] = [];

        $data["poles"][] = $this->preparePoleData($pole, $devices);
        return $data;
    }
    protected function calHeightOfDevice($x, $y, $z, $center_x = 0, $center_y = 0, $center_z = 0)
    {
        if ($z == null) return 0;
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

    protected function processingAntenna($devices): array
    {
        // sort devices asc by DC
        usort($devices, function($a, $b){
            return $b["DC"] - $a["DC"];
        });
        $level = 0;
        $dc = -1;
        $ans = [];
        $MAX_DELTA_DC = 1;
        foreach ($devices as $device){
            if (abs($device["DC"] - $dc) > $MAX_DELTA_DC ){
                $level++;
                $dc = $device["DC"];
            }else{
                $n_device = count($ans["T".$level]);
                $dc = ($device["DC"] + $dc * $n_device)/($n_device + 1);
            }
            $ans["T".$level][] = $device;
        }
        // merge devices have same ID in a level
        $result = [];
        foreach($ans as $level => $levelDevices){
            $result[$level] = [];
            $deviceMap = [];
            foreach($levelDevices as $device){
                $id = $device["id"];
                if(!isset($deviceMap[$id])){
                    $deviceMap[$id] = $device;
                    $deviceMap[$id]["quantity"] = 1;
                }else{
                    $deviceMap[$id]["quantity"]++;
                }
            }
            foreach($deviceMap as $device){
                $result[$level][] = $device;
            }
        }
        return $result;
    }

    protected function splitAndProcessingDevices($inputDevices): array
    {
        // prepare category
        $deviceCategories = DeviceCategory::all();
        $data = [];
        foreach($deviceCategories as $category) $data[$category->slug] = [];

        // Prepare input Devices
        foreach($inputDevices as $inputDevice){
            $device = Device::where("name",$inputDevice["name"])->first();
            if(!$device) continue;
            $inputDevice["id"] = $device->id;
            $inputDevice["weight"] = $device->weight;
            $inputDevice["category"] =$device->category->slug;
            $inputDevice["quantity"] = 1;
            $data[$device->category->slug][] =  $inputDevice;
        }

        $data["antenna"] = $this->processingAntenna($data["antenna"]);
        return $data;
    }

    protected function preparePoleData($pole, $devices): array
    {
        $poleParam = $pole->poleParam;
        // Get information of pole
        $data["pole_height"] = $poleParam->height;
        $data["pole_is_roof"] = $poleParam->is_roof ? "TM" : "DD";
        $data["pole_house_height"] = $poleParam->house_height;
        $data["pole_category"] = $pole->category->code;
        $data['pole_size'] = $poleParam->size;
        $data['pole_diameter_body_tube'] = $poleParam->diameter_body_tube;
        if (str_contains($data["pole_category"], 'TD'))
            $data["pole_category"] = "TD";

        $data["pole_devices"] = $this->splitAndProcessingDevices($devices);
        return $data;
    }

    protected function exportExcel($data): void
    {
        $filePath  = storage_path('app/public/sample/PoleStress_template.xlsx');

        // Mở file Excel
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Input');
        $row = 3;

        foreach($data["poles"] as $pole){
            $row++;
            $sheet->setCellValue('A'.$row, $data["station_code"]);
            $sheet->setCellValue('B'.$row, $data["windy_area"]);
            $sheet->setCellValue('C'.$row, $pole["pole_height"]);
            $sheet->setCellValue('D'.$row, $pole["pole_is_roof"]);
            $sheet->setCellValue('E'.$row, $pole["pole_house_height"] ?? 0);
            $sheet->setCellValue('F'.$row, $pole["pole_category"]);
            $sheet->setCellValue('G'.$row, $pole["pole_size"]);
            $sheet->setCellValue('H'.$row, $pole["pole_diameter_body_tube"]);

            $startColAntennaT1 = 'I';
            $startColAntennaT2 = 'U';
            $startColAntennaT3 = 'AG';
            $startColAntennaT4 = 'AS';
            $startColAntennaT5 = 'BE';
            $startColRRU = 'BQ';
            $startColViba = 'CF';

            $antennaDevices = $pole["pole_devices"]["antenna"] ?? [];
            foreach ($antennaDevices as $level => $devices){
                $startCol = $startColAntennaT1;
                if ($level == 'T2') $startCol = $startColAntennaT2;
                if ($level == 'T3') $startCol = $startColAntennaT3;
                if ($level == 'T4') $startCol = $startColAntennaT4;
                if ($level == 'T5') $startCol = $startColAntennaT5;
                foreach ($devices as $device){
                    $sheet->setCellValue($startCol.$row, $device["depth"]);
                    $startCol++;
                    $sheet->setCellValue($startCol.$row, $device["width"]);
                    $startCol++;
                    $sheet->setCellValue($startCol.$row, $device["height"]);
                    $startCol++;
                    $sheet->setCellValue($startCol.$row, $device["weight"]);
                    $startCol++;
                    $sheet->setCellValue($startCol.$row, number_format($device["DC"],2));
                    $startCol++;
                    $sheet->setCellValue($startCol.$row, $device["quantity"]);
                    $startCol++;
                }
            }

            $rruDevices = $pole["pole_devices"]["rru"] ?? [];
            foreach ($rruDevices as $device){
                $sheet->setCellValue($startColRRU.$row, number_format($device["DC"],2));
                $startColRRU++;
            }
            $vibaDevices = $pole["pole_devices"]["viba"] ?? [];
            foreach ($vibaDevices as $device){
                $sheet->setCellValue($startColViba.$row, number_format($device["DC"],2));
                $startColViba++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        //$saveFile = storage_path('app/public/temp/export/pole_stress_'.time().'.xlsx');
        $saveFile = "D:\ungsuat\ung_suat.xlsx";
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($saveFile);
    }

    public function poleStress(PoleStressRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $input = $request->all();
            $pole_id = $input["pole_id"];
            $devices = $input["devices"];

            $data = $this->prepareStationData($pole_id, $devices);

            // save data to excel
            $this->exportExcel($data);

            // Call MSTower
            //set_time_limit(300);
            //shell_exec('UiRobot.exe -file D:/ungsuat/MSTower.1.0.12.nupkg -input "{\"excelPath\":\"D:\\\\ungsuat\\\\ung_suat.xlsx\"}"');
            // read data from excel
            //$filePath = "D:\ungsuat\ung_suat.xlsx";
            //$data = Excel::toArray((object)null, $filePath);
            //$ans["pole_stress"] = (float)(str_replace(["_x000D_", "\n"], '', $data[1][3][89]))*100;
            $ans["pole_stress"] = 85;
            return ApiResponse::success($ans  , ApiMessage::POLE_STRESS_SUCCESS);
        }
        catch (\Exception $e){
            return ApiResponse::error([$e->getMessage()], ApiMessage::ERROR);
        }
     }

}
