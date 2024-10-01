<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Models\Station;
use App\Models\StationCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class StationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(): JsonResponse
    {
        $stationCategories = StationCategory::withCount('scans')
            ->having('scans_count', '>', 0)->with(['scans', 'location', 'address','scans.models'])
            ->get()->makeHidden(['location_id', 'address_id', 'stations_count']);

        return ApiResponse::success($stationCategories, ApiMessage::STATION_LIST);
    }

    public function detail($id): JsonResponse
    {
        $station = Station::with(['detail','poles','images','models','poles.devices',
            'images.gps', 'images.cameraPose', 'images.gimbal', 'poles.devices.category','poles.devices.params', 'poles.devices.vendor'
        ])->find($id);
        if (!$station) {
            return ApiResponse::error(null, ApiMessage::STATION_NOT_FOUND, 404);
        }
        return ApiResponse::success($station, ApiMessage::STATION_DETAIL);
    }

    public function listCode(): JsonResponse
    {
        $stations = Station::all()->select('id','code');
        return ApiResponse::success($stations, ApiMessage::STATION_LIST);
    }

    public function exportExcel(Request $request)
    {
        $input = $request->query('stations');
        $station_ids = [];
        foreach ($input as $item) $station_ids[] = (int)$item;

        $stations = Station::whereIn('id', $station_ids)->get();
        if (count($stations) == 0) return ApiResponse::error(["params" =>  $input], ApiMessage::STATION_EXPORT_FAIL, 404);


        // Mở file Excel
        $filePath  = storage_path('app/public/sample/Stations_Report_template.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('CỘT');
        $startRow = 5;
        $startCol = 'A';
        foreach ($stations as $index => $station) {
            $startRow++;
            $pole = $station->poles->first();
            $sheet->setCellValue('A' . $startRow, $index + 1);
            $sheet->setCellValue('B' . $startRow, 'Khu vực 1');
            $sheet->setCellValue('C' . $startRow, substr($station->code, 0, 3));
            $sheet->setCellValue('D' . $startRow, $station->code);
            $sheet->setCellValue('E' . $startRow, $station->detail->location->longitude);
            $sheet->setCellValue('F' . $startRow, $station->detail->location->latitude);
            $sheet->setCellValue('G' . $startRow, $station->detail->address->address_detail);
            $sheet->setCellValue('H' . $startRow, $station->detail->address->commune->windyArea->name);
            $sheet->setCellValue('I' . $startRow, $pole->category->code);
            $sheet->setCellValue('J' . $startRow, $pole->is_roof ? "TM" : "DD");
            $sheet->setCellValue('K' . $startRow, "Tự động");
            $sheet->setCellValue('L' . $startRow, $pole->height);
            $sheet->setCellValue('M' . $startRow, "Tự động");
            $sheet->setCellValue('N' . $startRow, $pole->house_height ?? 'X');
            $sheet->setCellValue('O' . $startRow, "X");
            $sheet->setCellValue('P' . $startRow, $pole->foot_size ?? 'X');
            $sheet->setCellValue('Q' . $startRow, $pole->top_size ??  'X');
            $sheet->setCellValue('R' . $startRow, $pole->foot_size || $pole->top_size ? 'Tự động' : 'X');
            $sheet->setCellValue('S' . $startRow, $pole->diameter_body_tube ?? 'X');
            $sheet->setCellValue('T' . $startRow, $pole->diameter_strut_tube ?? 'X');
            $sheet->setCellValue('U' . $startRow, $pole->diameter_body_tube || $pole->diameter_strut_tube ? 'Tự động' : 'X');
            $sheet->setCellValue('V' . $startRow, $pole->tilt_angle ?? 'X');
            $sheet->setCellValue('W' . $startRow, $pole->tilt_angle ? 'Tự động' : 'X');
        }
        $sheet = $spreadsheet->getSheetByName('THIẾT BỊ TRÊN CỘT');
        $startRow = 4;
        foreach ($stations as $index => $station) {
            $startRow++;
            $pole = $station->poles->first();
            $sheet->setCellValue('A' . $startRow, ($index + 1) . "." . $station->code);
            $devices = $pole->devices;
            foreach ($devices as $device) {
                $startRow++;
                $sheet->setCellValue('A' . $startRow, $station->code);
                $sheet->setCellValue('B' . $startRow, $device->category->name);
                $sheet->setCellValue('C' . $startRow, $device->name);
                $sheet->setCellValue('D' . $startRow, $device->vendor != null ? $device->vendor->name: '');
                $sheet->setCellValue('E' . $startRow, $device->height);
                $sheet->setCellValue('F' . $startRow, $device->width);
                $sheet->setCellValue('G' . $startRow, $device->depth);
                $sheet->setCellValue('H' . $startRow, $device->weight);
                $sheet->setCellValue('I' . $startRow, 'Tự động');
                $sheet->setCellValue('J' . $startRow, $device->pivot->height ?? 0 + $pole->house_height?? 0 );
                $sheet->setCellValue('K' . $startRow, 'Tự động');
                $sheet->setCellValue('L' . $startRow, $device->pivot->height ?? 0);
                $sheet->setCellValue('M' . $startRow, 'Tự động');
                $sheet->setCellValue('N' . $startRow, $device->pivot->tilt ?? 0);
                $sheet->setCellValue('O' . $startRow, 'Tự động');
                $sheet->setCellValue('P' . $startRow, $device->pivot->azimuth ?? 0);
                $sheet->setCellValue('Q' . $startRow, 'Tự động');
            }
        }

        $writer = new Xlsx($spreadsheet);

        $filePath = 'temp/export/' . 'stations_report_' . time() . '.xlsx';
        $saveFile =storage_path("app/public/".$filePath);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($saveFile);
        return ApiResponse::success(['file' => url(Storage::url($filePath))], ApiMessage::DEVICE_LIST);
    }
}
