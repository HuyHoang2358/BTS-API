<?php

namespace App\Models\Image;

use App\Models\Geometry\GeometryCone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CameraPose extends Model
{
    protected $table = 'camera_poses';
    protected $fillable = [
        'image_id',
        'w2c',
        'tvec',
        'qvec',
        'intrinsic_mtx',
        'geometry_cone_id'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public function geometryCone(): BelongsTo
    {
        return $this->belongsTo(GeometryCone::class);
    }
}
