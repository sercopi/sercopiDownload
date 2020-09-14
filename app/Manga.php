<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    protected $fillable = ["name", "author", "imageInfo", "alternativeTitle", "artist", "genre", "type", "status", "synopsis", "chapters", "score"];
    public function users()
    {
        return $this->belongsToMany("App\User");
    }
    public function comments()
    {
        return $this->morphMany("App\Comment", "commentable");
    }
}
