<?php

namespace App\Models\Pole;

use App\Models\Device\Device;
use App\Models\Geometry\GeometryBox;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find($pole_device_id)
 * @method static create(array $array)
 * @method static findOrFail($id)
 * @method static where(string $string, $pole_id)
 */
class PoleDevice extends Model
{
   protected $table = 'pole_devices';
   protected $fillable = [
       'pole_id',
       'device_id',
       'geometry_box_id',
       'rotation',
       'translation',
       'vertices',
       'tilt',
       'azimuth',
       'ai_device_width',
       'ai_device_height',
       'ai_device_depth',
       'suggested_img',
       'user_id',
       'is_active',
   ];
    protected $hidden = ['created_at', 'updated_at'];

    public function geometryBox():  BelongsTo
    {
        return $this->belongsTo(GeometryBox::class);
    }

    public function deviceInfo():  BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
    public function suggestedDevices(): HasMany
    {
        return $this->hasMany(SuggestedDevice::class);
    }


}
