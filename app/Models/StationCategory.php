<?php

namespace App\Models;

use App\Models\Address\Address;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static where(string $string, $stationCode)
 * @method static create(array $only)
 * @method static findOrFail($id)
 * @method static withCount(string $string)
 * @property mixed $address
 */
class StationCategory extends Model
{
    protected $table = 'station_categories';
    protected $fillable = ['name', 'code', 'description', 'location_id', 'address_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Station::class, 'station_category_id', 'id');
    }
}
