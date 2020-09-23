<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    public function ratings()
    {
        return $this->morphMany("App\Rating", "ratingable");
    }
    public function updateRating()
    {
        $rating = $this->ratings()->select(DB::raw("AVG(rating) as rating"))->first()->rating;
        if ($rating) {
            $this->update(["score" => round($rating, 2)]);
        }
        return round($rating, 2);
    }
}
