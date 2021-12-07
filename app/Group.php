<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];
    public function groupImage()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }
    public function messages()
    {
        return $this->hasMany(message::class, 'group_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_group');
    }
    public function getGroupImageAttribute()
    {
        if ($this->groupImage()->exists()) {
            return $this->groupImage()->latest()->first()->path;
        } else {
            return 'default.png';
        }
    }
}
