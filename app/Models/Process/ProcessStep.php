<?php

namespace App\Models\Process;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ProcessStep extends Model
{
    protected $table= 'process_steps';
    protected $fillable = ['process_id','step_number','step_name','current_progress','total_progress','status'];
    public function process(): BelongsTo
    {
        return $this->belongsTo(DataProcessingProcess::class);
    }
    public function logs(): HasMany
    {
        return $this->hasMany(ProcessLog::class, 'process_step_id');
    }
}
