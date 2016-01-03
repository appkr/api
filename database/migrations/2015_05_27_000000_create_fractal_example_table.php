<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFractalExampleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });

        Schema::create('things', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('author_id');
            $table->text('description')->nullable();
            $table->enum('deprecated', [0, 1])->default(0);
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
        Schema::drop('authors');
        Schema::drop('things');
    }

}
