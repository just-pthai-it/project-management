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
        Schema::create('notification_user', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->unsignedMediumInteger('notification_id');
            $table->unsignedMediumInteger('user_id');
            $table->timestamp('read_at')->nullable();

            $table->unique(['notification_id', 'user_id']);

            $table->foreign('notification_id')->references('id')->on('notifications');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists('notification_user');
    }
};
