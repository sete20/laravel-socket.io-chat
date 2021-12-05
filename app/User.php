<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    protected  $appends = ['personalImage'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function friends()
    {
        return $this->belongsToMany('App\User', 'users_friends', 'user_id', 'friend_id')->withPivot('friend_id');
    }

    public function personalImage()
    {
        return $this->morphOne('App\Models\File', 'fileable');
    }
    public function getPersonalImageAttribute()
    {
        if ($this->personalImage()->exists()) {
            return $this->personalImage()->latest()->first()->path;
        } else {
            return 'default.png';
        }
    }
    public function messages()
    {
        return $this->hasMany(message::class, 'sender_id');
    }
    public function receivedMessages()
    {
        return $this->hasMany(message::class, 'receiver_id');
    }
}
