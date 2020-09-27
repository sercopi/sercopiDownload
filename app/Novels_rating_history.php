<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Novels_rating_history extends Model
{
    protected $table = "novels_rating_history";
    protected $fillable = ["manga_id", "score"];
}
