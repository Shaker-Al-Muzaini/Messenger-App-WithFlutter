<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * @method static create(array $array)
 *@method static where(string$string, string$string1, string$string2)
 */
class Conversation extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'label',
        'last_message_id',
        'type',
        ];

    public function participants (): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants')
            ->withPivot([
                'joined_at', 'role'
            ]);

    }

    public  function  messages(): HasMany
    {
        return $this->hasMany(Message::class,'conversation_id','id');

    }

    public function recipients()
    {
        return $this->hasManyThrough(
            Recipient::class,
            Message::class,
            'conversation_id',
            'message_id',
            'id',
            'id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id', 'id')
            ->whereNull('deleted_at')
            ->withDefault([
                'body' => 'Message deleted'
            ]);
    }
}
