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
        Schema::create('project_user', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->unsignedMediumInteger('project_id');
            $table->unsignedMediumInteger('user_id');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);

            $table->foreign('project_id')->references('id')->on('projects');
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
        Schema::dropIfExists('project_user');
    }
};
