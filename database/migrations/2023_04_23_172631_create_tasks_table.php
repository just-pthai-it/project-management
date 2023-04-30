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
    public function up() : void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->unsignedMediumInteger('project_id');
            $table->unsignedMediumInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->unsignedSmallInteger('duration');
            $table->unsignedTinyInteger('status_id');
            $table->string('pending_reason')->nullable();
            $table->unsignedMediumInteger('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('task_statuses');
            $table->foreign('parent_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() : void
    {
        Schema::dropIfExists('tasks');
    }
};
