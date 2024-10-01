<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static create(array $array)
 */
class Image extends Model
{
    protected $table = 'images';
    protected $fillable = [
        'station_id',
        'image_url',
        'preview_image_url',
        'filename',
        'width',
        'height',
        'take_date'
    ];
    protected $hidden = ['created_at', 'updated_at', 'station_id'];


    public function gps(): HasOne
    {
        return $this->hasOne(GpsLocations::class, 'image_id', 'id');
    }

    public function cameraPose(): HasOne
    {
        return $this->hasOne(CameraPose::class, 'image_id', 'id');
    }

    public function gimbal(): HasOne
    {
        return $this->hasOne(Gimbal::class, 'image_id', 'id');
    }
}
