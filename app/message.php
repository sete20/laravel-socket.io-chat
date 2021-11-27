<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    protected $guarded = [];
    public function UserMessages()
    {
        return $this->hasMany(UserMessage::class);
    }
    public function Users()
    {
        return $this->belongsToMany(User::class, 'user_messages', 'message_id', 'sender_id')
            ->withTimestamps();
    }
}
