<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('space_exceptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('space_id');
            $table->foreign('space_id')->references('id')->on('spaces');
            $table->string('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('space_exceptions');
    }
};
