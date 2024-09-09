<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceParam extends Model
{
    protected $table = 'device_params';
    protected $fillable = ['device_id', 'key', 'value'];
    protected $hidden = ['created_at', 'updated_at', 'device_id','id'];
}
