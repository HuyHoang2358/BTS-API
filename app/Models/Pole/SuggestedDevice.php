<?php

namespace App\Models\Pole;

use App\Models\Device\Device;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuggestedDevice extends Model
{
    protected $table = 'suggested_devices';
    protected $fillable = ['pole_device_id', 'device_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }
}
