<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\DataProcessing;
use App\Helpers\QueryHelper;
use App\Http\Requests\Process\CreateProcessRequest;
use App\Jobs\ProcessingImage;
use App\Models\Device\Device;
use App\Models\Image\Image;
use App\Models\Pole\Pole;
use App\Models\Pole\PoleCategory;
use App\Models\Pole\PoleDevice;
use App\Models\Process\DataProcessingProcess;
use App\Models\Process\ProcessStep;
use App\Models\Station;
use App\Models\StationCategory;
use App\Models\StationPole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class DataFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // TODO: Step 1 Init data
    public function init($station_code, $date)
    {
        $stationCategory = StationCategory::where('code', $station_code)->first();

        // create new station record
        $newStation = Station::create([
            'name' => $station_code. ' - '. $date,
            'code'  => $station_code,
            'station_category_id' => $stationCategory ? $stationCategory->id : null,
            'date' => $date,
            'status' => 0,
        ]);

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
        // Get metadata from json file
        $metadataArray = [];
        $imageFilenames = [];
        if($imageMetadataJson && File::exists($imageMetadataJson)) {
            $metadata = json_decode(File::get($imageMetadataJson), true);
            foreach ($metadata as $key => $value) {
                $newKey = pathinfo($key, PATHINFO_FILENAME);
                $metadataArray[$newKey] = $value;
                $imageFilenames[]   = $newKey;
            }
        }

        // Get all images in folder
        if($imageFolder)
            $imageFilenames = DataProcessing::getImages($imageFolder);

        // Create process step
        $process = DataProcessingProcess::find($process_id);
        if (!$process) return;
        $station = Station::find($process->station_id);
        if (!$station) return;

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

        // Process each image
        foreach ($imageFilenames as $imageFilename) {
            $imageKey = pathinfo($imageFilename, PATHINFO_FILENAME);
            $src_path = $imageFolder . $imageFilename;
            $dst_path = $previewImageFolder . $imageFilename;



            $metadata = array_key_exists($imageKey, $metadataArray) ? $metadataArray[$imageKey] : [];
            $imageMetadata = DataProcessing::getMetadata($src_path, $metadata);
            DataProcessing::resizeImage($src_path, $dst_path, 200, 200);


            //TODO: save image information to database
            $image = Image::create([
                'station_id' => $process->station_id,
                'image_url' => Config::get('system.storage_domain'). Config::get('system.storage_bucket'). '/'. $station->code.'/'.$station->date.'/raw/'.$imageFilename,
                'preview_image_url' => Config::get('system.storage_domain'). Config::get('system.storage_bucket'). '/'. $station->code.'/'.$station->date.'/processedData/previewImages/'.$imageFilename,
                'filename' => $imageFilename,
                'width' => $imageMetadata['width'] ?? null,
                'height' => $imageMetadata['height'] ?? null,
                'take_date' => $imageMetadata['take_date'] ?? $station->date,
            ]);

            $image->gps()->create([
                'latitude' =>$imageMetadata["GPS"]["GPSLatitude"] ?? null,
                'longitude' =>$imageMetadata["GPS"]["GPSLongitude"] ?? null,
                'altitude' =>$imageMetadata["GPS"]["GPSAltitude"] ?? null,
                'latitude_ref' =>$imageMetadata["GPS"]["GPSLatitudeRef"] ?? null,
                'longitude_ref' =>$imageMetadata["GPS"]["GPSLongitudeRef"] ?? null,
                'altitude_ref' =>$imageMetadata["GPS"]["GPSAltitudeRef"] ?? null,
            ]);

            $image->gimbal()->create([
                'roll_degree' =>$imageMetadata["camera"]["GimbalRollDegree"] ?? null,
                'yaw_degree' =>$imageMetadata["camera"]["GimbalYawDegree"] ?? null,
                'pitch_degree' =>$imageMetadata["camera"]["GimbalPitchDegree"] ?? null,
            ]);

            $image->cameraPose()->create([
                'w2c' => json_encode($imageMetadata["camera_pose"]["w2c"] ?? []),
                'tvec' => json_encode($imageMetadata["camera_pose"]["tvec"] ?? []),
                'qvec' => json_encode($imageMetadata["camera_pose"]["qvec"] ?? []),
                'intrinsic_mtx' => json_encode($imageMetadata["camera_pose"]["intrinsic_mtx"] ?? []),
                'cent_point' => json_encode($imageMetadata["camera_pose"]["cent_point"] ?? []),
                'euler_angle' => json_encode($imageMetadata["camera_pose"]["euler_angle"] ?? []),
            ]);

            $process_step->update([
                'current_progress' => $process_step->current_progress + 1,
            ]);
            break;
        }
        $process_step->update([
            'status' => __('system.process.status.done'),
        ]);
    }

    // TODO: Step 3.1 Generate 3D Model
    public function generate3DModel($process_id): void
    {
        // Create process step
        $process = DataProcessingProcess::find($process_id);
        if (!$process) return;
        $process_step = $process->steps()->create([
            'step_number' => 3,
            'step_name' => __('system.process.step.generate_3d_model.name'),
            'current_progress' => 0,
            'total_progress' => 3,
            'status' => __('system.process.status.init'),
        ]);

        $process_step->update([
            'status' => __('system.process.status.processing'),
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Generating ply from images",
        ]);
    }

    // TODO: Step 3.2 Generated 3D Model
    public function generated3DModel($process_id): void
    {
        $process = DataProcessingProcess::find($process_id);
        if (!$process) return;

        $station = Station::find($process->station_id);
        if (!$station) return;

        $process_step = $process->steps()->where('step_number', 3)->first();
        if (!$process_step) return;

        $process_step->update([
            'current_progress' => 1,
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Generated ply from images",
        ]);

        // save ply model to database
        $model3D = $station->models()->create([
            'station_id' => $station->id,
            'filename' => $station->code.'_'.$station->date.'_model3D.ply',
            'preview_img' => '',
            'file_path' => Config::get('system.storage_bucket')."/".$station->code.'/'.$station->date.'/processedData/model3D/'.$station->code.'_'.$station->date.'_model3D.ply',
            'url' => Config::get('system.storage_domain').Config::get('system.storage_bucket')."/".$station->code.'/'.$station->date.'/processedData/model3D/'.$station->code.'_'.$station->date.'_model3D.ply',
            'type' => 'ply',
        ]);
    }

    // TODO: Step 3.3 Convert Model 3D
    public function convertModel3D($process_id): void
    {
        $process = DataProcessingProcess::find($process_id);
        if (!$process) return;
        $station = Station::find($process->station_id);
        if (!$station) return;

        $process_step = $process->steps()->where('step_number', 3)->first();
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "converting ply to las format",
        ]);

        $process_step->update([
            'current_progress' => 2,
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "converted ply to las format",
        ]);

        // save las model to database
        $model3D = $station->models()->create([
            'station_id' => $station->id,
            'filename' => $station->code.'_'.$station->date.'_model3D.las',
            'preview_img' => '',
            'file_path' => Config::get('system.storage_bucket')."/".$station->code.'/'.$station->date.'/processedData/model3D/'.$station->code.'_'.$station->date.'_model3D.las',
            'url' => Config::get('system.storage_domain').Config::get('system.storage_bucket')."/".$station->code.'/'.$station->date.'/processedData/model3D/'.$station->code.'_'.$station->date.'_model3D.las',
            'type' => 'las',
        ]);

        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "converting las to potree format",
        ]);
        $process_step->update([
            'current_progress' => 3,
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "converted las to potree format",
        ]);

        $process_step->update([
            'status' => __('system.process.status.done'),
        ]);

        // save potree model tu database
        $model3D = $station->models()->create([
            'station_id' => $station->id,
            'filename' => $station->code.'_'.$station->date.'_metadata.json',
            'preview_img' => '',
            'file_path' =>Config::get('system.storage_bucket')."/".$station->code.'/'.$station->date.'/processedData/model3D/potree/'.$station->code.'_'.$station->date.'_metadata.json',
            'url' => Config::get('system.storage_domain').Config::get('system.storage_bucket')."/".$station->code.'/'.$station->date.'/processedData/model3D/potree/'.$station->code.'_'.$station->date.'_metadata.json',
            'type' => 'potree',
        ]);
    }

    // TODO: Step 4 Extract Information from label file
    public function extractInformation($process_id, $labelPath): void
    {
        $process = DataProcessingProcess::find($process_id);
        if (!$process) return;
        $station = Station::find($process->station_id);
        if (!$station) return;


        // Get data from label json file
        $label = ($labelPath && File::exists($labelPath)) ? json_decode(File::get($labelPath), true) : [];
        $poles = $label['poles'] ?? [];

        $process_step = $process->steps()->create([
            'step_number' => 4,
            'step_name' => __('system.process.step.extract_information.name'),
            'current_progress' => 0,
            'total_progress' => count($poles),
            'status' => __('system.process.status.init'),
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Total Poles: ".count($poles),
        ]);
        $num_poles = count($poles);
        foreach ($poles as $index => $pole) {
            $process_step->logs()->create([
                'process_step_id' => $process_step->id,
                'logs' => "Processing pole " . $index,
            ]);
            $isRoof = ($pole['mounting_position'] ?? '') == 'TM';
            $pole_category = PoleCategory::where('code', $pole['type'] ?? '')->first();

            // Create new pole
            $newPole = Pole::create([
                'name' =>$pole_category ? $pole_category->name . ($num_poles >1 ? '_' . $index + 1 : '') : 'Chưa xác định',
                'height' => $pole['tower_height'] ?? null,
                'is_roof' => ($pole['mounting_position'] ?? '') == 'TM',
                'house_height' => $isRoof ? $pole['house_height'] ?? null : 0,
                'pole_category_id' => $pole_category ? $pole_category->id : 0,
                'size' => $pole['tower_size'] ?? null,
                'diameter_body_tube' => $pole['tower_diameter_body_tube'] ?? null,
                'diameter_strut_tube' => $pole['tower_diameter_strut_tube'] ?? null,
                'diameter_top_tube' => $pole['tower_diameter_top_tube'] ?? null,
                'diameter_bottom_tube' => $pole['tower_diameter_bottom_tube'] ?? null,
                'foot_size' => $pole['tower_foot_size'] ?? null,
                'top_size' => $pole['tower_top_size'] ?? null,
                'structure' => $pole['tower_structure'] ?? null,
                'description' => $pole['description'] ?? null,
                'z_plane' => $pole['z_plane'] ?? null,
                'north_direction' => json_encode($pole['north_direction'] ?? []),
                'gps_ratio' =>$pole['gps_ratio'] ?? null,
                'tilt_angle' => $pole['tower_tilt'] ?? null,
                'param_a' => $pole['param_a'] ?? null,
                'param_b' => $pole['param_b'] ?? null,
                'stress_value' => $pole['stress_value'] ?? null,
                'is_shielded' => $pole['is_shielded'] ?? null,
            ]);

            // Match pole to station
            $station_pole = StationPole::create([
                'station_id' => $station->id,
                'pole_id' => $newPole->id,
                'built_on' => null,
            ]);

            // Match device to pole
            foreach ($pole['objects'] as $object) {
                $suggestedDevice = count($object) > 0 ? $object[0] : null;
                if (!$suggestedDevice) continue;

                $suggestedModels = [];
                foreach ($object as $item) $suggestedModels[] = $item['model'];

                $device = Device::where('name', trim($suggestedDevice['model']?? ''))->first();
                if (!$device) continue;

                PoleDevice::create([
                    'pole_id' => $newPole->id,
                    'device_id' => $device->id,
                    'attached_at' => null,
                    'x' => null,
                    'y' => null,
                    'z' => null,
                    'alpha' => null,
                    'beta' => null,
                    'gama' => null,
                    'height' => $suggestedDevice['device_height'] ?? null,
                    'description'=> '',
                    'tilt' => $suggestedDevice['device_tilt'] ?? null,
                    'azimuth' => $suggestedDevice['device_azimuth'] ?? null,
                    'vertices' => json_encode($suggestedDevice['vertices'] ?? []),
                    'translation' => json_encode($suggestedDevice['translation'] ?? []),
                    'rotation' =>  json_encode($suggestedDevice['rotation'] ?? []),
                    'suggested_devices' => json_encode($suggestedModels),
                    'suggested_img' => '',
                ]);
            }
        }

        $process_step->update([
            'current_progress' => count($poles),
            'status' => __('system.process.status.done'),
        ]);
    }


    // TODO: Index
    public function index(): JsonResponse
    {
        $query = DataProcessingProcess::query();
        $processes = QueryHelper::applyQuery(
            $query,
            request(),
            ['station_id'],
            ['station', 'steps'],
            ['station_id']
        );

        return ApiResponse::success($processes);
    }
    public function store(CreateProcessRequest $request): JsonResponse
    {
        $input = $request->validated();
        // convert format date remove '-'
        $date = $input['date'];
        $station_code = $input['station_code'];

        // Step 1: Init data
        $process_id = $this->init($station_code, $date);

        // Step 2: Image Processing
        $dataFolder = 'D:/OSPanel/domains/BTS-API/storage/app/public/data/';
        $srcImageFolder = 'E:/Viettel/Projects/BTS/data/bts/images/'.$station_code."/";
        $srcImageFolder = '';
        $imageMetadataJson = $dataFolder.$station_code.'/'.$date.'/processedData/'.$station_code.'_'.$date.'_images_metadata.json';
        ProcessingImage::dispatch($station_code, $date, $srcImageFolder, $srcImageFolder.'preview/', $imageMetadataJson);

        // Step 3: Generate 3D Model
        $this->generate3DModel($process_id);
        $this->generated3DModel($process_id);
        $this->convertModel3D($process_id);

        // Step 4: Extract Information
        $labelPath = $dataFolder.$station_code.'/'.$date.'/processedData/'.$station_code.'_'.$date.'_label.json';
        $this->extractInformation($process_id, $labelPath);

        $process = DataProcessingProcess::find($process_id);
        $process->update([
            'status' => 1,
        ]);

        return ApiResponse::success([]);
    }



    public function test(): JsonResponse
    {
        $srcImageFolder = 'E:/Viettel/Projects/BTS/data/bts/images/HTY1877/';
        $imageFilenames = DataProcessing::getImages($srcImageFolder);
        foreach ($imageFilenames as $imageFilename){
            $imageKey = pathinfo($imageFilename, PATHINFO_FILENAME);
            $src_path = $srcImageFolder . $imageFilename;
            $dst_path = $srcImageFolder . 'preview/' . $imageKey . '.jpg';
            DataProcessing::createThumbnail($src_path, $dst_path);
            break;
        }
        return ApiResponse::success([]);
    }
}
