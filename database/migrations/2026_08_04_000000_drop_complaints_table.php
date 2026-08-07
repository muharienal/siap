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
        Schema::dropIfExists('complaints');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category', 50);
            $table->text('description');
            $table->string('evidence_path', 255)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('admin_response')->nullable();
            $table->timestamps();
        });
    }
};
