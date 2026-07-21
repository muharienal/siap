<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambahkan kolom ke employees
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'role')) {
                $table->tinyInteger('role')->default(2)->after('password')->comment('1=Admin, 2=User');
            }
            if (!Schema::hasColumn('employees', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('employees', 'remember_token')) {
                $table->string('remember_token', 100)->nullable()->after('is_active');
            }
        });

        // 2. Pindahkan data dari users ke employees
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            DB::table('employees')
                ->where('id', $user->employee_id)
                ->update([
                    'password' => $user->password,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'remember_token' => $user->remember_token,
                ]);
        }

        // 3. Drop foreign key yang mengarah ke users
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['processed_by']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // 4. Hapus tabel users
        Schema::dropIfExists('users');
    }

    public function down()
    {
        // Rollback: buat ulang tabel users
        // (Tidak perlu diimplementasikan secara detail untuk produksi)
    }
};