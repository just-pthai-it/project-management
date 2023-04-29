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
        Schema::create('task_constraints', function (Blueprint $table)
        {
            $table->unsignedMediumInteger('id')->autoIncrement();
            $table->unsignedMediumInteger('task_id');
            $table->unsignedMediumInteger('constraint_task_id');
            $table->unsignedTinyInteger('constraint_id');
            $table->timestamps();

            $table->unique(['task_id', 'constraint_task_id']);

            $table->foreign('task_id')->references('id')->on('tasks');
            $table->foreign('constraint_task_id')->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('task_constraints');
    }
};
