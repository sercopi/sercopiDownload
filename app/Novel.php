<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Novel extends Model
{
    protected $fillable = ["name", "author", "alternativeTitle", "status", "genre", "synopsis", "score", "imageInfo"];

    public function users()
    {
        return $this->belongsToMany("App\User");
    }
    public function novel_chapters()
    {
        return $this->hasMany("App\NovelChapter");
    }
    public function comments()
    {
        return $this->morphMany("App\Comment", "commentable");
    }
}
