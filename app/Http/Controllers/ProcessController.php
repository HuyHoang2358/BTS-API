<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\Process\CreateProcessRequest;
use App\Models\Device\Device;
use App\Models\Geometry\GeometryBox;
use App\Models\Geometry\GeometryCone;
use App\Models\Image\Image;
use App\Models\Pole\Pole;
use App\Models\Pole\PoleCategory;
use App\Models\Pole\PoleDevice;
use App\Models\Process\Process;
use App\Models\Scan;
use App\Models\Station;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class ProcessController extends Controller
{
    // TODO: common functions
    private function reformatMetaData($metadata): array
    {
        $data = [];
        $data['width'] = $metadata['Width'] ?? null;
        $data['height'] = $metadata['Height'] ?? null;
        $data['take_date'] = isset($metadata['take_date']) ? str_replace('_', '-',$metadata['take_date']) : null;

        $data["GPS"]["GPSAltitude"] = $metadata['GPSAltitude'] ?? null;
        $data["GPS"]["GPSLatitude"] = $metadata['GPSLatitude'] ?? null;
        $data["GPS"]["GPSLongitude"] = $metadata['GPSLongitude'] ?? null;
        $data["GPS"]["GPSAltitudeRef"]  = $metadata['GPSAltitudeRef'] ?? null;
        $data["GPS"]["GPSLatitudeRef"] = $metadata["GPSLatitudeRef"] ?? null;
        $data["GPS"]["GPSLongitudeRef"]  = $metadata["GPSLongitudeRef"] ?? null;

        $data["camera"]["GimbalRollDegree"] = $metadata["GimbalRollDegree"] ?? null;
        $data["camera"]["GimbalYawDegree"] = $metadata["GimbalYawDegree"] ?? null;
        $data["camera"]["GimbalPitchDegree"] = $metadata["GimbalPitchDegree"] ?? null;
        $data["camera"]["Make"] = null;
        $data["camera"]["Model"] = null;
        $data["camera"]["Software"] = null;
        $data["camera"]["DateTime"] = null;

        $data["camera_pose"]["w2c"] = $metadata["w2c"] ?? [];
        $data["camera_pose"]["tvec"] = $metadata["tvec"] ?? [];
        $data["camera_pose"]["qvec"] = $metadata["qvec"] ?? [];
        $data["camera_pose"]["intrinsic_mtx"] = $metadata["intrinsic_mtx"] ?? [];
        $data["camera_pose"]["cent_point"] = $metadata["cam_cent"] ?? [];
        $data["camera_pose"]["euler_angle"] = $metadata["euler_angle"] ?? [];
        return $data;
    }

    private function handleCesiumIonAccessID($station_code): string
    {
        return match ($station_code) {
            'HTY1877' => '2753957',
            'HNI4067' => '2736433',
            'HAN0240' => '2736511',
            'HAN0212' => '2743152',
            default => '',
        };
    }

    private function handleStressValue($station_code): float
    {
        return match ($station_code) {
            'HAN1188' => 73.4,
            'HAN0240' => 47.5,
            'HAN0212' => 43.2,
            default => 0,
        };
    }

    // TODO: Step 1 Init data
    private function init($station_code, $date): void
    {
        $station = Station::where('code', $station_code)->first();

        // create new station record
        $scan = Scan::create([
            'name' => $station_code. '_'. $date,
            'station_id'  => $station->id,
            'status' => 'Khởi tạo',
            'date' => $date,
        ]);

        // Create new  processing data process
        $process = Process::create([
            'scan_id' => $scan->id,
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
            'logs' => __('system.process.step.init.logs.init') . $station_code . ' ' . $date,
        ]);
        $process_log->processStep()->update([
            'current_progress' => 1,
            'status' => __('system.process.status.done'),
        ]);
        $this->startProcessingImage($station_code, $date);
    }

    // TODO: Step 2: start processing image
    private function startProcessingImage($station_code, $date): void
    {
        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->create([
            'step_number' => 2,
            'step_name' => __('system.process.step.image_processing.name'),
            'current_progress' => 0,
            'total_progress' => 1,
            'status' => __('system.process.status.processing'),
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Bắt đầu xử lý hình ảnh 2D",
        ]);
    }

    // TODO: Step 2: finish processing image
    public function finishProcessingImage(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];
        $imageMetadataJsonPath = $input['image_metadata_json_path'];

        $metadataArray = [];
        $imageFilenames = [];
        if($imageMetadataJsonPath && File::exists($imageMetadataJsonPath)) {
            $metadata = json_decode(File::get($imageMetadataJsonPath), true);
            foreach ($metadata as $key => $value) {
                $newKey = pathinfo($key, PATHINFO_FILENAME);
                $metadataArray[$newKey] = $value;
                $imageFilenames[]   = $newKey;
            }
        }

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->where('step_number', 2)->first();
        if (!$process_step) return;
        $process_step->update([
            'current_progress' => 0,
            'total_progress' => count($imageFilenames),
        ]);
        $numImages = count($imageFilenames);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Tổng số ảnh: ".$numImages,
        ]);

        $index = 0;
        foreach ($imageFilenames as $imageKey) {
            $metadata = array_key_exists($imageKey, $metadataArray) ? $this->reformatMetaData($metadataArray[$imageKey]) : [];
            try{
                //TODO: save image information to database
                $image = Image::create([
                    'scan_id' => $scan->id,
                    'image_url' => Config::get('system.storage_domain') . Config::get('system.storage_bucket') . '/' . $station_code . '/' . $date . '/raw/' . $imageKey . '.JPG',
                    'preview_image_url' => Config::get('system.storage_domain') . Config::get('system.storage_bucket') . '/' . $station_code . '/' . $date . '/processedData/previewImages/' . $imageKey . ".webp",
                    'filename' => $imageKey . '.JPG',
                    'width' => $metadata['width'] ?? null,
                    'height' => $metadata['height'] ?? null,
                    'take_date' => $metadata['take_date'] ?? $date,
                ]);
                $image->gps()->create([
                    'latitude' => $metadata["GPS"]["GPSLatitude"] ?? null,
                    'longitude' => $metadata["GPS"]["GPSLongitude"] ?? null,
                    'altitude' => $metadata["GPS"]["GPSAltitude"] ?? null,
                    'latitude_ref' => $metadata["GPS"]["GPSLatitudeRef"] ?? null,
                    'longitude_ref' => $metadata["GPS"]["GPSLongitudeRef"] ?? null,
                    'altitude_ref' => $metadata["GPS"]["GPSAltitudeRef"] ?? null,
                ]);
                $image->gimbal()->create([
                    'roll_degree' => $imageMetadata["camera"]["GimbalRollDegree"] ?? null,
                    'yaw_degree' => $imageMetadata["camera"]["GimbalYawDegree"] ?? null,
                    'pitch_degree' => $imageMetadata["camera"]["GimbalPitchDegree"] ?? null,
                ]);
                $geometry_cone = GeometryCone::create([
                    'radius' => 0.09,
                    'height' => 0.15,
                    'radial_segments' => 24,
                    'pos_x' => $metadata["camera_pose"]["cent_point"][0] ?? null,
                    'pos_y' => $metadata["camera_pose"]["cent_point"][1] ?? null,
                    'pos_z' => $metadata["camera_pose"]["cent_point"][2] ?? null,
                    'rotate_x' => $metadata["camera_pose"]["euler_angle"][0] ?? null,
                    'rotate_y' => $metadata["camera_pose"]["euler_angle"][1] ?? null,
                    'rotate_z' => $metadata["camera_pose"]["euler_angle"][2] ?? null,
                ]);
                $image->cameraPose()->create([
                    'w2c' => json_encode(["camera_pose"]["w2c"] ?? []),
                    'tvec' =>  json_encode($metadata["camera_pose"]["tvec"] ?? []),
                    'qvec' =>  json_encode($metadata["camera_pose"]["qvec"] ?? []),
                    'intrinsic_mtx' =>  json_encode($metadata["camera_pose"]["intrinsic_mtx"] ?? []),
                    'geometry_cone_id' =>  $geometry_cone->id,
                ]);
                $index ++;
            } catch (Exception $e) {
                $process_step->logs()->create([
                    'process_step_id' => $process_step->id,
                    'logs' => $imageKey . ":  " . $e->getMessage(),
                ]);
            }
        }
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Đã xử lý xong ". $index."/". $numImages. " hình ảnh",
        ]);

    }

    // TODO: Step 3.1: start generating 3D model
    public function startGenerating3DModel(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
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
            'logs' => "Bắt đầu tạo mô hình 3D từ hình ảnh",
        ]);
    }

    // TODO: Step 3.1: finish generating 3D model
    public function finishGenerating3DModel(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];
        $file_path = $input['file_path'];
        $filename = $input['filename'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->where('step_number', 3)->first();
        if (!$process_step) return;



        $scan->models()->create([
            'scan_id' => $scan->id,
            'filename' => $filename,
            'preview_img' => '',
            'file_path' => $file_path,
            'url' => Config::get('system.storage_domain').$file_path,
            'type' => 'ply',
        ]);
        $process_step->update([
            'current_progress' => 1,
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Đã tạo mô hình 3D định dạng ply cho trạm " . $station_code,
        ]);
    }

    // TODO: Step 3.2: start convert model 3D to 3D las
    public function startConvert3DModelToLas(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->where('step_number', 3)->first();
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Bắt đầu chuyển đổi mô hình 3D trạm ".$station_code. " sang định dạng las",
        ]);
    }
    // TODO: Step 3.2: finish convert model 3D to 3D las
    public function finishConvert3DModelToLas(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];
        $file_path = $input['file_path'];
        $filename = $input['filename'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->where('step_number', 3)->first();

        // save las model to database
        $scan->models()->create([
            'scan_id' => $scan->id,
            'filename' => $filename,
            'preview_img' => '',
            'file_path' => $file_path,
            'url' => $file_path,
            'type' => 'las',
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Đã chuyển đổi mô hình 3D trạm ".$station_code. " sang định dạng las",
        ]);
        $process_step->update([
            'current_progress' => 2,
        ]);
    }

    // TODO: Step 3.3: Convert las to potree
    public function startConvertLasToPotree(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->where('step_number', 3)->first();
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Bắt đầu chuyển đổi mô hình las của trạm " . $station_code. " sang định dạng potree",
        ]);


    }
    // TODO: Step 3.3: finish convert las to potree
    public function finishConvertLasToPotree(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];
        $file_path = $input['file_path'];
        $filename = $input['filename'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        $process_step = $process->steps()->where('step_number', 3)->first();

        // save las model to database
        $scan->models()->create([
            'scan_id' => $scan->id,
            'filename' => $filename,
            'preview_img' => '',
            'file_path' => $file_path,
            'url' => Config::get('system.storage_domain').$file_path,
            'type' => 'potree',
        ]);

        $process_step->update([
            'current_progress' => 3,
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Đã chuyển đổi mô hình las của trạm " . $station_code. " sang định dạng potree",
        ]);
    }

    // TODO: Step 4: Extract information
    public function ExtractedInformation(Request $request): void
    {
        $input = $request->all();
        $station_code = $input['station_code'];
        $date = $input['date'];
        $labelPath = $input['label_path'];

        $scan = Scan::where('name', $station_code. '_'. $date)->first();
        if (!$scan) return;
        $process = Process::where('scan_id', $scan->id)->first();
        if (!$process) return;

        // Get data from label json file
        $label = ($labelPath && File::exists($labelPath)) ? json_decode(File::get($labelPath), true) : [];
        $poles = $label['poles'] ?? [];
        $num_poles = count($poles);
        $process_step = $process->steps()->create([
            'step_number' => 4,
            'step_name' => __('system.process.step.extract_information.name'),
            'current_progress' => 0,
            'total_progress' => count($poles),
            'status' => __('system.process.status.init'),
        ]);
        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Bắt đầu import thông tin trạm: ". $station_code. " vào hệ thống",
        ]);

        foreach ($poles as $index => $pole) {
            $process_step->logs()->create([
                'process_step_id' => $process_step->id,
                'logs' => "Đang import thông tin cột thứ " . ($index+1). " của trạm " .$station_code. " vào hệ thống",
            ]);
            $isRoof = ($pole['mounting_position'] ?? '') == 'TM';
            $pole_category = PoleCategory::where('code', $pole['type'] ?? '')->first();

            // Create new pole
            $newPole = Pole::create([
                'scan_id' => $scan->id,
                'name' => ($pole_category ? $pole_category->name : 'Cột' ). ($num_poles >1 ? '_' . $index + 1 : ''),
                'pole_category_id' => $pole_category ? $pole_category->id : 0,
                'z_plane' => $pole['z_plane'] ?? null,
                'plane_altitude' => $pole['z_plane'] ?? 0,
                'gps_ratio' => $pole['gps_ratio'] ?? null,
                'stress_value' => $this->handleStressValue($station_code),
            ]);

            // Create pole params
            $newPole->poleParams()->create([
                'pole_id' => $newPole->id,
                'height' => $pole['tower_height'] ?? null,
                'is_roof' => ($pole['mounting_position'] ?? '') == 'TM',
                'house_height' => $isRoof ? $pole['house_height'] ?? null : 0,
                'diameter_body_tube' => $pole['tower_diameter_body_tube'] ?? null,
                'diameter_strut_tube' => $pole['tower_diameter_strut_tube'] ?? null,
                'diameter_top_tube' => $pole['tower_diameter_top_tube'] ?? null,
                'diameter_bottom_tube' => $pole['tower_diameter_bottom_tube'] ?? null,
                'tilt_angle' => $pole['tower_tilt'] ?? null,
                'is_shielded' => $pole['is_shielded'] ?? 0,
                'size' => $pole['tower_size'] ?? null,
                'foot_size' => $pole['tower_foot_size'] ?? null,
                'top_size' => $pole['tower_top_size'] ?? null,
                'description' => $pole['description'] ?? $station_code. " " . $date,
                'user_id' => 0,
                'is_active' => 1,
            ]);

            // Match device to pole
            $numDevice = 0;
            foreach ($pole['objects'] as $object) {
                if (count($object) == 0) continue;
                $poleDeviceInfo = $object[0];
                $device = Device::where('name', trim($poleDeviceInfo['model'] ?? ''))->first();
                if (!$device) continue;

                // Create geometry box
                $geometry_box = GeometryBox::create([
                    'depth' => 1,
                    'width' => 1,
                    'height' => 1,
                    'pos_x' => 1,
                    'pos_y' => 1,
                    'pos_z' => 1,
                    'rotate_x' => 1,
                    'rotate_y' => 1,
                    'rotate_z' => 1
                ]);

                // Create pole device
                $poleDevice = PoleDevice::create([
                    'pole_id' => $newPole->id,
                    'device_id' => $device->id,
                    'geometry_box_id' => $geometry_box->id,
                    'rotation' => json_encode($poleDeviceInfo['rotation'] ?? []),
                    'translation' => json_encode($poleDeviceInfo['translation'] ?? []),
                    'vertices' => json_encode($poleDeviceInfo['vertices'] ?? []),
                    'tilt' => $poleDeviceInfo['tilt'] ?? null,
                    'azimuth' =>  $poleDeviceInfo['device_azimuth'] ?? null,
                    'ai_device_width' => $poleDeviceInfo['model_width'] ?? null,
                    'ai_device_height' => $poleDeviceInfo['model_height'] ?? null,
                    'ai_device_depth' => $poleDeviceInfo['model_depth'] ?? null,
                    'suggested_img' => '',
                    'user_id' => 0,
                    'is_active' => 1
                ]);

                // Create Suggested device
                foreach ($object as $item){
                    $device = Device::where('name', trim($item['model']?? ''))->first();
                    if (!$device) continue;
                    $poleDevice->suggestedDevices()->create([
                        'device_id' => $device->id,
                        'pole_device_id' => $poleDevice->id,
                    ]);
                }
                $numDevice ++;


            }

            $process_step->logs()->create([
                'process_step_id' => $process_step->id,
                'logs' => "Đã import thông tin: ".$numDevice . " thiết bị",
            ]);
        }

        $process_step->logs()->create([
            'process_step_id' => $process_step->id,
            'logs' => "Quá trình import thông tin trạm: ". $station_code. " đã hoàn thành",
        ]);
    }
    // TODO: Create a new process to process the data
    public function store(CreateProcessRequest $request): JsonResponse
    {
        $input = $request->all();
        $date = $input['date'];
        $station_code = $input['station_code'];

        // Step 1: Init data
        $this->init($station_code, $date);

        // Step 2: Finish processing image
        $finishProcessingImageRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
            'image_metadata_json_path' => storage_path('app/public/data/'.$station_code.'/'.$date.'/processedData/'.$station_code.'_'.$date.'_images_metadata.json'),
        ]);
        $this->finishProcessingImage($finishProcessingImageRequest);

        // Step 3.1: startGenerating 3D model
        $startGenerating3DModelRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
        ]);
        $this->startGenerating3DModel($startGenerating3DModelRequest);

        $finishGenerating3DModelRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
            'file_path' => Config::get('system.storage_bucket')."/".$station_code.'/'.$date.'/processedData/model3D/'.$station_code.'_'.$date.'_model3D.ply',
            'filename' => $station_code.'_'.$date.'_model.ply',
        ]);
        $this->finishGenerating3DModel($finishGenerating3DModelRequest);

        // Step 3.2: startConvert 3D model to las
        $startConvert3DModelToLasRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
        ]);
        $this->startConvert3DModelToLas($startConvert3DModelToLasRequest);
        $finishConverted3DModelToLasRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
            'file_path' => $this->handleCesiumIonAccessID($station_code),
            'filename' => $station_code.'_'.$date.'_model.las',
        ]);
        $this->finishConvert3DModelToLas($finishConverted3DModelToLasRequest);

        // Step 3.2: startConvert las model to potree
        $startConvertLasToPotreeRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
        ]);
        $this->startConvertLasToPotree($startConvertLasToPotreeRequest);
        $finishConvertLasToPotreeRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
            'file_path' => Config::get('system.storage_bucket')."/".$station_code.'/'.$date.'/processedData/model3D/potree/'.$station_code.'_'.$date.'_metadata.json',
            'filename' => $station_code.'_'.$date.'_metadata.json',
        ]);
        $this->finishConvertLasToPotree($finishConvertLasToPotreeRequest);

        // Step 4: Extract information
        $extractedInformationRequest = new Request([
            'station_code' => $station_code,
            'date' => $date,
            'label_path' => storage_path('app/public/data/'.$station_code.'/'.$date.'/processedData/'.$station_code.'_'.$date.'_label.json'),
        ]);
        $this->ExtractedInformation($extractedInformationRequest);


        $scan = Scan::with(['process', 'process.steps', 'process.steps.logs'])->where('name', $station_code. '_'. $date)->first();
        $scan->update([
            'status' => 'Hoàn thành',
            'is_active' => 1,
        ]);

        return ApiResponse::success($scan);
    }
}
