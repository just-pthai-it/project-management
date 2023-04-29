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
        Schema::create('project_statuses', function (Blueprint $table)
        {
            $table->unsignedTinyInteger('id')->autoIncrement();
            $table->string('name');
            $table->string('color');
            $table->boolean('is_permanent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('project_statuses');
    }
};
