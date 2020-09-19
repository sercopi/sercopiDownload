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
        'name', 'email', 'password', "role_id", "foto_id"
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
    public function role()
    {
        return $this->belongsTo("\App\Role");
    }
    public function foto()
    {
        return $this->belongsTo("\App\Foto");
    }
    public function esAdmin()
    {
        return $this->role->nombre === "Administrador";
    }
    public function esUser($givenName)
    {
        return $this->name === $givenName;
    }
    public function mangas()
    {
        return $this->belongsToMany("\App\Manga");
    }
    public function comments()
    {
        return $this->hasMany("App\Comment");
    }
    public function novels()
    {
        return $this->belongsToMany("App\Novel");
    }
}
