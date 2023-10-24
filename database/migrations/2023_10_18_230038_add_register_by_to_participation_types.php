<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('participation_types', function (Blueprint $table) {
            $table->unsignedBigInteger('register_by')->default(1)->after('name');

            $table->foreign('register_by')
                  ->references('id')
                  ->on('users');
        });
    }

    public function down()
    {
        Schema::table('participation_types', function (Blueprint $table) {
            $table->dropForeign(['register_by']);
            $table->dropColumn('register_by');
        });
    }
};
