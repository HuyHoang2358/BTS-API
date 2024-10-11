<?php

namespace App\Models\Process;

use App\Models\Scan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static create(array $array)
 * @method static where(string $string, $id)
 */
class Process extends Model
{
    protected $table= 'processes';
    protected $fillable = ['scan_id','status'];
    protected $hidden = ['created_at', 'updated_at'];

    public function steps(): HasMany
    {
        return $this->hasMany(ProcessStep::class, 'process_id', 'id');
    }
    public function scan(): HasOne
    {
        return $this->hasOne(Scan::class, 'id', 'scan_id');
    }
}

