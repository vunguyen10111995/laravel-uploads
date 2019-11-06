<?php

namespace App;

use App\Models\Upload;
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

    public function getAvatar()
    {
        $file = $this->getUploadedAvatar();

        $handler = app('upload')->handler('avatar');

        return $file ? $handler->url($file) : $handler->getDefaultAvatarUrl();
    }

    public function getUploadedAvatar()
    {
        if (!$this->avatarExisted()) {
            return null;
        }
        return $this->avatarImage;
    }

    public function avatarImage()
    {
        return $this->hasOne(Upload::class, 'hashed_name', 'avatar');
    }

    protected function avatarExisted()
    {
        return array_key_exists('avatar', $this->getAttributes()) && !is_null($this->getAttribute('avatar'));
    }
}
