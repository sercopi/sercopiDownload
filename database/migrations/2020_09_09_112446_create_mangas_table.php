<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMangasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mangas', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->longText("imageInfo");
            $table->text("alternativeTitle")->charset('utf8mb4')->nullable(true);
            $table->string("author")->charset('utf8mb4');
            $table->string("artist")->charset('utf8mb4');
            $table->string("genre");
            $table->string("type");
            $table->string("status");
            $table->text("synopsis");
            $table->text("chapters");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mangas');
    }
}
