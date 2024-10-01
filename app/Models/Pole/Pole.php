<?php

namespace App\Models\Pole;

use App\Models\Device\Device;
use App\Models\Station;
use App\Models\StationPole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static find($pole_id)
 * @property mixed $id
 */
class Pole extends Model
{
    protected $table = 'poles';

    protected $fillable = [
        'name',
        'height',
        'is_roof',
        'house_height',
        'pole_category_id',
        'size',
        'diameter_body_tube',
        'diameter_strut_tube',
        'diameter_top_tube',
        'diameter_bottom_tube',
        'foot_size',
        'top_size',
        'structure',
        'z_plane',
        'north_direction',
        'gps_ratio',
        'tilt_angle',
        'param_a',
        'param_b',
        'description',
        'stress_value',
        'is_shielded'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function ($pole) {
            // detach all poles devices
            $pole->devices()->detach();

            // delete all params;
            $pole->params()->delete();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PoleCategory::class, 'pole_category_id');
    }

    public function params(): HasMany
    {
        return $this->hasMany(PoleParam::class);
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'pole_device', 'pole_id', 'device_id')
            ->withPivot('id','attached_at', 'x', 'y', 'z', 'alpha', 'beta', 'gama','rotation','translation', 'vertices','tilt', 'azimuth','height','suggested_devices', 'suggested_img','description');
    }
    // add station code to atrribute of pole


    public function stations(): BelongsToMany
    {
        return $this->belongsToMany(Station::class, 'station_pole', 'pole_id', 'station_id')
            ->withPivot('built_on');
    }

    protected $appends = ['station_code'];

    public function getStationCodeAttribute()
    {
        $stationPole = StationPole::where('pole_id', $this->id)->first();
        if (!$stationPole) {
            return null;
        }
        return $stationPole->station_code;
    }
}
