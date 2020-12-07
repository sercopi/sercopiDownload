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
    public function novel_update_history()
    {
        return $this->hasMany("App\Novels_update_history");
    }
    public function novel_rating_history()
    {
        return $this->hasMany("App\Novels_rating_history");
    }
    public function comments()
    {
        return $this->morphMany("App\Comment", "commentable");
    }
    public function ratings()
    {
        return $this->morphMany("App\Rating", "ratingable");
    }

    //function to run everytime a rating is issued by an user,
    // it calculates a mean for the rating of a resource
    //and updates it, returning said new rating to pass to the client
    public function updateRating()
    {
        $rating = $this->ratings()->select(DB::raw("AVG(rating) as rating"))->first()->rating;
        if ($rating) {
            $this->update(["score" => round($rating, 2)]);
            $this->novel_rating_history()->create(["score" => $rating]);
        }
        return round($rating, 2);
    }
    public function follows()
    {
        return $this->morphMany("App\Follow", "followable");
    }
}
