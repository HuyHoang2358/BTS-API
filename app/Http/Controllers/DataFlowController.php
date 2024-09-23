<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\DataProcessing;
use App\Http\Requests\Process\CreateProcessRequest;
use App\Models\Process\DataProcessingProcess;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class DataFlowController extends Controller
{
    // TODO: Step 1 Init data
    public function init($input)
    {
        $stationCode = $input['station_code'];
        $date = $input['date'];
        $station = Station::where('code', $stationCode)->where('status', 1)->first();
        if (!$station) return null;

        // create new station record
        $newStation = $station->replicate();
        $newStation->date = $date;
        $newStation->status = 0;
        $newStation->save();

        // Create new  processing data process
        $process = DataProcessingProcess::create([
            'station_id' => $newStation->id,
            'status' => 0
        ]);
        // create process step
        $process_step = $process->steps()->create([
            'step_number' => 1,
            'step_name' => __('system.process.step.init.name'),
            'current_progress' => 0,
            'total_progress' => 1,
            'status' => __('system.process.status.init'),
        ]);
        // create log of process
        $process_log = $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => __('system.process.step.init.logs.init'),
        ]);
        $process_log->processStep()->update([
            'current_progress' => 1,
            'status' => __('system.process.status.done'),
        ]);

        return $process->id;
    }

    // TODO: Step 2 Image Processing
    public function imageProcessing($process_id, $imageFolder, $previewImageFolder, $imageMetadataJson = null): void
    {
        $metadataArray = [];
        if($imageMetadataJson && File::exists($imageMetadataJson)) {
            $metadata = json_decode(File::get($imageMetadataJson), true);
            foreach ($metadata as $key => $value) {
                $newKey = pathinfo($key, PATHINFO_FILENAME);
                $metadataArray[$newKey] = $value;
            }
        }

        $imageFilenames = DataProcessing::getImages($imageFolder);

        $process = DataProcessingProcess::find($process_id);
        if (!$process) return;

        $process_step = $process->steps()->create([
            'step_number' => 2,
            'step_name' => __('system.process.step.image_processing.name'),
            'current_progress' => 0,
            'total_progress' => count($imageFilenames),
            'status' => __('system.process.status.init'),
        ]);

        $process_step->update([
            'status' => __('system.process.status.processing'),
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Total Images: ".count($imageFilenames),
        ]);
        foreach ($imageFilenames as $imageFilename) {
            $imageKey = pathinfo($imageFilename, PATHINFO_FILENAME);
            $src_path = $imageFolder . $imageFilename;
            $dst_path = $previewImageFolder . $imageFilename;

            $process_step->logs()->create([
                'process_step_id' => $process_step->id,
                'logs' => "Processing Image: ".$imageFilename,
            ]);

            $metadata = array_key_exists($imageKey, $metadataArray) ? $metadataArray[$imageKey] : [];
            $imageMetadata = DataProcessing::getMetadata($src_path, $metadata);
            //DataProcessing::resizeImage($src_path, $dst_path, 200, 200);
            //TODO: save image information to database

            $process_step->update([
                'current_progress' => $process_step->current_progress + 1,
            ]);
            //break;
        }
        $process_step->update([
            'status' => __('system.process.status.done'),
        ]);
    }

    public function convertModel3D($modelPath)
    {
        // save model information to database
        $data['modelPath'] = $modelPath;
        return $data;
    }

    public function extractInfomation($modelPath)
    {
        // save model information to database
        $data['extractInfomation'] = $modelPath;
        return $data;
    }


    public function index(){
        $stationCode = 'HAN-0212';
        $data["init"] = $this->init($stationCode);
        $data["imageProcessing"] = $this->imageProcessing();
        return ApiResponse::success($data);
        $data["convertModel3D"] = $this->convertModel3D('');
        $data["extractInformation"] = $this->extractInfomation('');
        $data["done"] = "Done";

        return ApiResponse::success($data);
    }

    public function store(CreateProcessRequest $request): JsonResponse
    {
        $input = $request->validated();
        $process_id = $this->init($input);
        //$process_id = 4;

        $srcImageFolder = 'E:/Viettel/Projects/BTS/data/bts/images/HAN0212/';
        $imageMetadataJson = 'D:/OSPanel/domains/BTS-API/storage/app/public/data/HAN-0212/20240921/processedData/HAN-0212_20240921_image_metadata.json';
        $this->imageProcessing($process_id, $srcImageFolder, $srcImageFolder.'thumb/', $imageMetadataJson);

        //$data["process_id"] = $process_id;
        return ApiResponse::success([]);
    }
}
