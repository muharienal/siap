<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('employees', 'nip')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unique('nip');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('employees', 'nip')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropUnique(['nip']);
            });
        }
    }
};