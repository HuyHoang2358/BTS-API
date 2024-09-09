<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static where(string $string, mixed $category)
 */
class DeviceCategory extends Model
{
    protected $table = 'device_categories';
    protected $fillable = ['name', 'description', 'slug'];
    protected $hidden = ['created_at', 'updated_at'];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
