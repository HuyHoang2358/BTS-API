<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 * @method static find($device_id)
 * @method static where(string $string, $id)
 * @property mixed $category
 */
class Device extends Model
{
    protected $table = 'devices';
    protected $fillable = [
        'name',
        'slug',
        'model',
        'images',
        'model_url',
        'length',
        'width',
        'depth',
        'weight',
        'diameter',
        'description',
        'device_category_id',
        'vendor_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // delete params before delete device
    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function ($device) {
            $device->params()->delete();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeviceCategory::class, 'device_category_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function params(): HasMany
    {
        return $this->hasMany(DeviceParam::class, 'device_id');
    }
}
