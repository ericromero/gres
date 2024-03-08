<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained();
            $table->foreignId('resource_type_id')->constrained();
            $table->string('name');
            $table->integer('inventory')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            // Restricción única para la combinación de name y space_id
            $table->unique(['name', 'department_id']);

        });
    }

    public function down()
    {
        Schema::dropIfExists('resources');
    }
};
