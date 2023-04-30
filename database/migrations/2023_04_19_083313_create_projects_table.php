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
        Schema::create('projects', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->string('name');
            $table->string('customer_name')->nullable();
            $table->text('summary')->nullable();
            $table->string('code');
            $table->unsignedMediumInteger('user_id');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->unsignedSmallInteger('duration');
            $table->unsignedTinyInteger('progress');
            $table->unsignedTinyInteger('status_id');
            $table->string('pending_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('project_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('projects');
    }
};
