<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
