<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Location;
use App\Models\Pole\Pole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static where(string $string, mixed $validated)
 */
class Station extends Model
{
    protected $table = 'stations';
    protected $fillable = ['name', 'code', 'description', 'location_id', 'address_id'];
    protected $hidden = ['created_at', 'updated_at'];

    // delete station ->remove all address, location, poles
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($station) {
            $station->location()->delete();
            $station->address()->delete();

            $stationPoles = StationPole::where('station_code', $station->code)->get();
            $station->poles()->detach();
            foreach($stationPoles as $stationPole){
                $pole = Pole::find($stationPole->pole_id);
                $pole->delete();
            }
        });
    }


    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
    public function poles(): BelongsToMany
    {
        return $this->belongsToMany(Pole::class, 'station_pole', 'station_code', 'pole_id', 'code')
            ->withPivot('built_on')->with('category');
    }


    public function model3Ds(): HasMany
    {
        return $this->hasMany(Model3D::class, 'station_id', 'id');
    }



}
