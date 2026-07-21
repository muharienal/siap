<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('employees', 'email')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('email')->nullable()->after('phone_number');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('employees', 'email')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};