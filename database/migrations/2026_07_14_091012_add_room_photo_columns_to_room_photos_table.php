<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Cek apakah kolom room_id sudah ada, jika belum tambahkan
        if (!Schema::hasColumn('room_photos', 'room_id')) {
            Schema::table('room_photos', function (Blueprint $table) {
                $table->foreignId('room_id')->after('id')->constrained()->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('room_photos', 'photo_path')) {
            Schema::table('room_photos', function (Blueprint $table) {
                $table->string('photo_path')->after('room_id');
            });
        }

        if (!Schema::hasColumn('room_photos', 'order')) {
            Schema::table('room_photos', function (Blueprint $table) {
                $table->integer('order')->default(0)->after('photo_path');
            });
        }
    }

    public function down()
    {
        // Tidak perlu drop, karena ini hanya penambahan kolom
    }
};