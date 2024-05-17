<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Hacer que ciertos campos puedan ser nulos
            $table->string('modality')->nullable()->change();
            $table->string('scope')->nullable()->change();
            $table->string('project_type')->nullable()->change();
            $table->string('gender_equality')->nullable()->change();
            $table->boolean('registration_required')->nullable()->change();
            $table->boolean('recording_required')->nullable()->change();
            $table->boolean('transmission_required')->nullable()->change();
            $table->boolean('published')->nullable()->change();
            $table->unsignedBigInteger('published_by')->nullable()->change();
            $table->boolean('cancelled')->nullable()->change();

            // Agregar el nuevo campo "private"
            $table->boolean('private')->default(false)->after('published_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Revertir los cambios
            $table->string('modality')->nullable(false)->change();
            $table->string('scope')->nullable(false)->change();
            $table->string('project_type')->nullable(false)->change();
            $table->string('gender_equality')->nullable(false)->change();
            $table->boolean('registration_required')->nullable(false)->change();
            $table->boolean('recording_required')->nullable(false)->change();
            $table->boolean('transmission_required')->nullable(false)->change();
            $table->boolean('published')->nullable(false)->change();
            $table->unsignedBigInteger('published_by')->nullable(false)->change();
            $table->boolean('cancelled')->nullable(false)->change();
            $table->dropColumn('private');
        });
    }
};
