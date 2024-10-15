<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(array $array)
 * @method static where(string $string, $model)
 */
class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['content', 'model', 'model_id', 'user_id'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
