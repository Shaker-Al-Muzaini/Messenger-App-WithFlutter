<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Message extends Model
{
    protected $fillable=[
        'conversation_id','user_id','body','type'
    ];
    use HasFactory ,SoftDeletes;

    public function conversation (): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
    public function user (): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name'=> __('User')
        ]);
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class,'recipients')
            ->withPivot([
                'read_at', 'deleted_at'
            ]);
    }

}
