<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
