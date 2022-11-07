<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static where(array $array)
 */
class Recipient extends pivot
{
    use HasFactory ,SoftDeletes;
    public $timestamps=false;
    protected $casts=[
        'read_at'=> 'datetime'
    ];
    public  function message (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
    public  function user (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
