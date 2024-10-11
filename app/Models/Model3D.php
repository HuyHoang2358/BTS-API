<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Model3D extends Model
{

    protected $table = 'models';
    protected $fillable = ['scan_id', 'filename', 'preview_img', 'url', 'file_path', 'type'];
    protected $hidden = ['created_at', 'updated_at','station_id'];
    // type: 1: potree, 2: las, 3: ply, 4: gltf

    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }
}

