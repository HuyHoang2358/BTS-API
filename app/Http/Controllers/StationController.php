<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Models\Scan;
use App\Models\Station;
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
        $stations = Station::withCount('scans')
            ->having('scans_count', '>', 0)->with(['scans', 'location', 'address','scans.models'])
            ->get()->makeHidden(['location_id', 'address_id', 'scans_count']);

        return ApiResponse::success($stations, ApiMessage::STATION_LIST);
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

    public function exportExcel(Request $request): JsonResponse
    {
        $input = $request->query('scans');
        $scan_ids = [];
        foreach ($input as $item) $scan_ids[] = (int)$item;

        $scans = Scan::whereIn('id', $scan_ids)->get();
        if (count($scans) == 0) return ApiResponse::error(["params" =>  $input], ApiMessage::STATION_EXPORT_FAIL, 404);


        // Mở file Excel
        $filePath  = storage_path('app/public/sample/Stations_Report_template.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('CỘT');
        $startRow = 5;
        $name =  '';
        foreach ($scans as $index => $scan) {
            $name = $scan->station->code;
            $startRow++;
            $pole = $scan->poles->first();
            $sheet->setCellValue('A' . $startRow, $index + 1); // STT
            $sheet->setCellValue('B' . $startRow, 'Khu vực 1'); // Khu vực
            $sheet->setCellValue('C' . $startRow, substr($scan->station->code, 0, 3)); // Mã Tỉnh
            $sheet->setCellValue('D' . $startRow, $scan->station->code); // Mã Trạm
            $sheet->setCellValue('E' . $startRow, $scan->station->location->longitude); // Kinh độ
            $sheet->setCellValue('F' . $startRow, $scan->station->location->latitude); // Vĩ độ
            $sheet->setCellValue('G' . $startRow, $scan->station->address->address_detail); // Vị trí
            $sheet->setCellValue('H' . $startRow, $scan->station->address->commune->windyArea->name); // Vùng gió
            $sheet->setCellValue('I' . $startRow, $pole->category->code); // Loại cột
            $sheet->setCellValue('J' . $startRow, $pole->poleParam->is_roof ? "TM" : "DD"); // Trên mái, dưới đất
            $sheet->setCellValue('K' . $startRow, "Tự động");
            $sheet->setCellValue('L' . $startRow, $pole->poleParam->height);
            $sheet->setCellValue('M' . $startRow, "Tự động");
            $sheet->setCellValue('N' . $startRow, $pole->poleParam->house_height ?? 'X');
            $sheet->setCellValue('O' . $startRow, "X");
            $sheet->setCellValue('P' . $startRow, $pole->poleParam->foot_size ?? 'X');
            $sheet->setCellValue('Q' . $startRow, $pole->poleParam->top_size ??  'X');
            $sheet->setCellValue('R' . $startRow, $pole->poleParam->foot_size || $pole->poleParam->top_size ? 'Tự động' : 'X');
            $sheet->setCellValue('S' . $startRow, $pole->poleParam->diameter_body_tube ?? 'X');
            $sheet->setCellValue('T' . $startRow, $pole->poleParam->diameter_strut_tube ?? 'X');
            $sheet->setCellValue('U' . $startRow, $pole->poleParam->diameter_body_tube || $pole->poleParam->diameter_strut_tube ? 'Tự động' : 'X');
            $sheet->setCellValue('V' . $startRow, $pole->poleParam->tilt_angle ?? 'X');
            $sheet->setCellValue('W' . $startRow, $pole->poleParam->tilt_angle ? 'Tự động' : 'X');
        }
        $sheet = $spreadsheet->getSheetByName('THIẾT BỊ TRÊN CỘT');
        $startRow = 4;
        foreach ($scans as $index => $scan) {
            $startRow++;
            $pole = $scan->poles->first();
            $sheet->setCellValue('A' . $startRow, ($index + 1) . "." . $scan->station->code);
            $poleDevices = $pole->poleDevices;
            foreach ($poleDevices as $poleDevice) {
                $startRow++;
                $sheet->setCellValue('A' . $startRow, $scan->station->code);
                $sheet->setCellValue('B' . $startRow, $poleDevice->deviceInfo->category->name);
                $sheet->setCellValue('C' . $startRow, $poleDevice->deviceInfo->name);
                $sheet->setCellValue('D' . $startRow, $poleDevice->deviceInfo->vendor != null ? $poleDevice->deviceInfo->vendor->name: '');
                $sheet->setCellValue('E' . $startRow, $poleDevice->deviceInfo->length);
                $sheet->setCellValue('F' . $startRow, $poleDevice->deviceInfo->width);
                $sheet->setCellValue('G' . $startRow, $poleDevice->deviceInfo->depth);
                $sheet->setCellValue('H' . $startRow, $poleDevice->deviceInfo->weight);
                $sheet->setCellValue('I' . $startRow, 'Tự động');
                $sheet->setCellValue('J' . $startRow, $poleDevice->height ?? 0 + $pole->poleParam->house_height ?? 0 );
                $sheet->setCellValue('K' . $startRow, 'Tự động');
                $sheet->setCellValue('L' . $startRow, $poleDevice->height ?? 0);
                $sheet->setCellValue('M' . $startRow, 'Tự động');
                $sheet->setCellValue('N' . $startRow, $poleDevice->tilt ?? 0);
                $sheet->setCellValue('O' . $startRow, 'Tự động');
                $sheet->setCellValue('P' . $startRow, $poleDevice->azimuth ?? 0);
                $sheet->setCellValue('Q' . $startRow, 'Tự động');
            }
        }

        $writer = new Xlsx($spreadsheet);

        $filePath = 'temp/export/' . 'stations_report_' . $name. "_" . time() . '.xlsx';
        $saveFile =storage_path("app/public/".$filePath);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($saveFile);
        return ApiResponse::success(['file' => url(Storage::url($filePath))], ApiMessage::DEVICE_LIST);
    }
}
