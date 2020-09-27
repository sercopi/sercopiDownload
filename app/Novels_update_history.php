<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Novels_update_history extends Model
{
    protected $table = "novels_upadte_history";
    protected $fillable = ["novel_id", "chapters_introduced"];
}
