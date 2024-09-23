<?php

namespace App\Models\Process;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static find($process_id)
 */
class DataProcessingProcess extends Model
{
    protected $table= 'processing_data_processes';
    protected $fillable = ['station_id','status'];

    public function steps(): HasMany
    {
        return $this->hasMany(ProcessStep::class, 'process_id', 'id');
    }
}
