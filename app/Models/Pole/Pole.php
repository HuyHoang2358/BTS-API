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
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'scan_id',
        'pole_category_id',
        'name',
        'z_plane',
        'plane_altitude',
        'gps_ratio',
        'stress_value',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected static function boot(): void
    {
        parent::boot();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PoleCategory::class, 'pole_category_id');
    }

    public function poleDevices(): HasMany
    {
        return $this->hasMany(PoleDevice::class)->where('is_active', 1);
    }

    public function poleParam(): HasOne
    {
        return $this->hasOne(PoleParam::class, 'pole_id', 'id')->where('is_active', 1);
    }
    public function poleParams(): HasMany
    {
        return $this->hasMany(PoleParam::class);
    }


}
