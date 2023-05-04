<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up () : void
    {
        Schema::create('comments', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->morphs('commentable');
            $table->string('content');
            $table->unsignedMediumInteger('user_id');
            $table->unsignedTinyInteger('deep_level');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('comments');
    }
};
