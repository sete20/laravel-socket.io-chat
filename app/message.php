<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    protected $guarded = [];

    // public function Users()
    // {
    //     return $this->belongsToMany(User::class, 'user_messages', 'message_id', 'sender_id')
    //         ->withTimestamps();
    // }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
