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
 * @method static whereIn(string $string, array $station_ids)
 * @property mixed $address
 */
class Station extends Model
{
    protected $table = 'stations';
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
        return $this->hasMany(Scan::class);
    }

    protected $appends = ['pole_category', "stress_value"];
    public function getPoleCategoryAttribute()
    {
       $poles = $this->scans()->first()->poles()->first();
       return $poles->category;
    }
    public function getStressValueAttribute()
    {
        $poles = $this->scans()->first()->poles()->first();
        return $poles->stress_value;
    }

}
