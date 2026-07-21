<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('room_photos', function (Blueprint $table) {
            // Cek apakah kolom belum ada, baru tambahkan
            if (!Schema::hasColumn('room_photos', 'room_id')) {
                $table->foreignId('room_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('room_photos', 'photo_path')) {
                $table->string('photo_path')->after('room_id');
            }
            if (!Schema::hasColumn('room_photos', 'order')) {
                $table->integer('order')->default(0)->after('photo_path');
            }
        });
    }

    public function down()
    {
        // Hapus kolom jika rollback
        Schema::table('room_photos', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['room_id', 'photo_path', 'order']);
        });
    }
};