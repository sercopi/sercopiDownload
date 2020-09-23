<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ["user_id", "rating", "ratingable_id", "ratingable_type"];
    public function ratingable()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo("App\User");
    }
}
