<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $fillable = ["user_id", "followable_id", "followable_type", "follow", "notifications"];
    protected $table = "follows";
    public function user()
    {
        return $this->belongsTo("App\User");
    }
    public function followable()
    {
        return $this->morphTo();
    }
}
