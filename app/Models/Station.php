<?php

namespace App\Models;

use App\Models\Image\Image;
use App\Models\Pole\Pole;
use App\Models\Process\DataProcessingProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static where(string $string, mixed $validated)
 * @method static find($station_id)
 * @method static whereIn(string $string, array $station_ids)
 */
class Station extends Model
{
    protected $table = 'stations';
    protected $fillable = ['name', 'code', 'date', 'status', 'station_category_id'];
    protected $hidden = ['created_at', 'updated_at'];

    // delete station ->remove all address, location, poles
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($station) {
            $station->location()->delete();
            $station->address()->delete();

            $stationPoles = StationPole::where('station_id', $station->id)->get();
            $station->poles()->detach();
            foreach($stationPoles as $stationPole){
                $pole = Pole::find($stationPole->pole_id);
                $pole->delete();
            }
        });
    }


   public function detail(): BelongsTo
    {
        return $this->belongsTo(StationCategory::class, 'station_category_id', 'id')
            ->with(['location', 'address']);
    }

    public function poles(): BelongsToMany
    {
        return $this->belongsToMany(Pole::class, 'station_pole', 'station_id', 'pole_id', 'id')
            ->withPivot('id','pole_id','station_id', 'built_on')
            ->with(['category']);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'station_id', 'id');
    }
    public function models(): HasMany
    {
        return $this->hasMany(Model3D::class, 'station_id', 'id');
    }

    public function processingDataProcesses(): HasMany
    {
        return $this->hasMany(DataProcessingProcess::class, 'station_id', 'id');
    }

    public function stationCategory(): BelongsTo
    {
        return $this->belongsTo(StationCategory::class);
    }

}

