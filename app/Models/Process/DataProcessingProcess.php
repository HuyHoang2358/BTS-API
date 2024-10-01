<?php

namespace App\Models\Process;

use App\Models\Station;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static find($process_id)
 * @method static where(string $string, $id)
 */
class DataProcessingProcess extends Model
{
    protected $table= 'processing_data_processes';
    protected $fillable = ['station_id','status'];
    protected $hidden = ['created_at', 'updated_at'];

    public function steps(): HasMany
    {
        return $this->hasMany(ProcessStep::class, 'process_id', 'id');
    }
    public function station(): HasOne
    {
        return $this->hasOne(Station::class, 'id', 'station_id');
    }
}
