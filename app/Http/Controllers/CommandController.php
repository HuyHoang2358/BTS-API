<?php

namespace App\Http\Controllers;

use App\Helpers\ExcelHelper;
use App\Models\Device\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Facades\Excel;

class CommandController extends Controller
{
    public function index(){
       /* $filePath = "hello";
        Artisan::call('test:calculation', [
            'file_path' => $filePath
        ]);
        return response()->json(['message' => 'Command is running in the background']);*/

       /* $filePath = "C:\Users\HuyHoang\Downloads\sample_DeviceVendor.xlsx";
        $data = Excel::toArray(null, $filePath);
        return response()->json(['data' => $data], 200);*/
        $devices = Device::all();
        foreach ($devices as $device){
            $device->images = '';
            $device->save();
        }
    }
}
