<?php

namespace App\Models;


use App\Models\Image\Image;
use App\Models\Pole\Pole;
use App\Models\Process\Process;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Scan extends Model
{
    protected $table = 'scans';
    protected $fillable = ['name', 'station_id', 'status', 'is_active', 'date'];
    protected $hidden = ['created_at', 'updated_at'];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(Model3D::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function poles(): HasMany
    {
        return $this->hasMany(Pole::class);
    }
    public function process(): HasOne
    {
        return $this->hasOne(Process::class);
    }


}
