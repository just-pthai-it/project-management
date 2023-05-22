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
        Schema::create('permissions', function (Blueprint $table)
        {
            $table->unsignedSmallInteger('id')->autoIncrement();
            $table->string('group_name');
            $table->string('name');
            $table->string('ability');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down () : void
    {
        Schema::dropIfExists('permissions');
    }
};
