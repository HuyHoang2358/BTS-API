<?php

namespace App\Models\Process;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ProcessLog extends Model
{
    use HasFactory;
    protected $table= 'process_logs';
    protected $fillable = ['process_step_id','logs'];

    public function processStep(): BelongsTo
    {
        return $this->belongsTo(ProcessStep::class);
    }
}
