<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nip', 50)->unique();
                $table->string('full_name');
                $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
                $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
                $table->enum('gender', ['L', 'P'])->default('L');
                $table->date('birth_date')->nullable();
                $table->string('phone_number', 20)->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('employment_status', 50)->default('Kontrak');
                $table->string('photo_path')->nullable();
                $table->string('password');
                $table->tinyInteger('role')->default(2)->comment('1=Admin, 2=User');
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};