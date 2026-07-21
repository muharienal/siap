<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('facilities', function (Blueprint $table) {
            if (Schema::hasColumn('facilities', 'stock_qty')) {
                $table->dropColumn('stock_qty');
            }
        });
    }

    public function down()
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->integer('stock_qty')->default(0)->after('description');
        });
    }
};