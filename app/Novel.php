<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Novel extends Model
{
    protected $fillable = ["name", "author", "alternativeTitle", "status", "genre", "synopsis", "score", "imageInfo"];

    public function user()
    {
        return $this->belongsTo("App\Novel");
    }
    public function novel_chapters()
    {
        return $this->hasMany("App\Novelchapter");
    }
}
