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
        Schema::create('activity_logs', function (Blueprint $table)
        {
            $table->id();
            $table->morphs('objectable');
            $table->unsignedTinyInteger('type_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('data')->nullable();
            $table->unsignedMediumInteger('user_id');
            $table->timestamp('created_at');

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
        Schema::dropIfExists('activity_logs');
    }
};
