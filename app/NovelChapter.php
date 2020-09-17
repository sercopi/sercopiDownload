<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NovelChapter extends Model
{
    protected $fillable = ["novel_id", "title", "content"];
    protected $table = "novel_chapters";
    public function novel()
    {
        return $this->belongsTo("App\Novel");
    }
}
