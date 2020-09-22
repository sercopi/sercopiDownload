<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    protected $fillable = ["comment", "rating"];
    public function commentable()
    {
        return $this->morhpTo();
    }
    public function user()
    {
        return $this->belongsTo("App\User");
    }
    public function likes()
    {
        return $this->hasMany("App\Like");
    }
    public function comments()
    {
        return $this->morphMany("App\Comment", "commentable");
    }
    public function getLikes()
    {
        $total = $this->likes()->select(DB::raw('SUM(likes.like) as total'))->first()->total;
        return !is_null($total) ? $total : 0;
    }
}
