<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class model3D extends Model
{

    protected $table = 'model_3ds';
    protected $fillable = ['name', 'station_id', 'file_name', 'preview_img', 'url', 'file_path', 'type'];
    // type: 1: potree, 2: las, 3: ply, 4: gltf

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
