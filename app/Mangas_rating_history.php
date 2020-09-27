<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mangas_rating_history extends Model
{
    protected $table = "mangas_rating_history";
    protected $fillable = ["manga_id", "score"];
}
