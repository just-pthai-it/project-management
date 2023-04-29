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
        Schema::create('notifications', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->morphs('notifiable');
            $table->unsignedTinyInteger('type')->nullable();
            $table->text('data')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists('notifications');
    }
};
