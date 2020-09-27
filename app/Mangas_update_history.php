<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mangas_update_history extends Model
{
    protected $table = ["mangas_update_history"];
    protected $fillable = ["manga_id", "chapters_introduced"];
}
