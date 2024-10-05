<?php

namespace App\Jobs;

use App\Helpers\DataProcessing;
use App\Models\Image\Image;
use App\Models\Process\DataProcessingProcess;
use App\Models\Station;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class ProcessingImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected string $station_code;
    protected string $date;
    protected string $imageFolder;
    protected string $previewImageFolder;
    protected array $metadataArray;
    protected array $imageFilenames;
    protected $process_step;
    protected $station;

    /**
     * Create a new job instance.
     */
    public function __construct($station_code, $date, $imageFolder, $previewImageFolder,$metadataJsonFilePath)
    {
        $this->station_code = $station_code;
        $this->date = $date;
        $this->imageFolder = $imageFolder;
        $this->previewImageFolder = $previewImageFolder;

        // Get metadata from json file
        $metadataArray = [];
        $imageFilenames = [];
        if($metadataJsonFilePath && File::exists($metadataJsonFilePath)) {
            $metadata = json_decode(File::get($metadataJsonFilePath), true);
            foreach ($metadata as $key => $value) {
                $imageKey = pathinfo($key, PATHINFO_FILENAME);
                $metadataArray[$imageKey] = $value;
                $imageFilenames[]   = $imageKey;
            }
        }

        // Get all images in folder
        if($imageFolder) $imageFilenames = DataProcessing::getImages($imageFolder);
        $this->metadataArray = $metadataArray;
        $this->imageFilenames = $imageFilenames;

        // Write log
        $station = Station::where('code', $station_code)->where('date', $date)->first();
        $this->station = $station;
        $process = DataProcessingProcess::where('station_id', $station->id)->first();

        $this->process_step = $process->steps()->create([
            'step_number' => 2,
            'step_name' => __('system.process.step.image_processing.name'),
            'current_progress' => 0,
            'total_progress' => count($imageFilenames),
            'status' => __('system.process.status.init'),
        ]);
        $this->process_step->update([
            'status' => __('system.process.status.processing'),
        ]);
        $this->process_step->logs()->create([
            'process_step_id' => $this->process_step->id,
            'logs' => "Total Images: ".count($imageFilenames),
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->imageFilenames as $imageKey) {
            $startTime = microtime(true);
            $this->process_step->logs()->create([
                'process_step_id' => $this->process_step->id,
                'logs' => "Đang xử lý ảnh: ".$imageKey,
            ]);

            $src_path = $this->imageFolder . $imageKey. '.JPG' ;
            $preview_path = $this->previewImageFolder . $imageKey . '.webp';

            $metadata = array_key_exists($imageKey, $this->metadataArray) ? $this->metadataArray[$imageKey] : [];
            $imageMetadata = DataProcessing::getMetadata($src_path, $metadata);

            if ($this->imageFolder) DataProcessing::createThumbnail($src_path, $preview_path);

            //TODO: save image information to database
            $image = Image::create([
                'station_id' => $this->station->id,
                'image_url' => Config::get('system.storage_domain'). Config::get('system.storage_bucket'). '/'. $this->station->code.'/'.$this->station->date.'/raw/'.$imageKey. '.JPG',
                'preview_image_url' => Config::get('system.storage_domain'). Config::get('system.storage_bucket'). '/'. $this->station->code.'/'.$this->station->date.'/processedData/previewImages/'.$imageKey.".webp",
                'filename' => $imageKey.'.JPG',
                'width' => $imageMetadata['width'] ?? null,
                'height' => $imageMetadata['height'] ?? null,
                'take_date' => $imageMetadata['take_date'] ?? $this->station->date,
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

            $this->process_step->update([
                'current_progress' => $this->process_step->current_progress + 1,
            ]);

            $this->process_step->logs()->create([
                'process_step_id' => $this->process_step->id,
                'logs' => "Đã xử lý ảnh: ".$imageKey. " - Thời gian xử lý: ".number_format((microtime(true) - $startTime), 2)." s",
            ]);
        }
    }
}
