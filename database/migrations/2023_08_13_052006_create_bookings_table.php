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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Peminjam');
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('purpose');
            $table->tinyInteger('status')->default(0)->comment('0=Pending, 1=Approved, 2=Rejected, 3=Cancelled');
            
            // Field baru untuk Override & Approval
            $table->string('booking_type', 20)->default('Regular')->comment('Regular / Priority (Direksi)');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null')->comment('ID Admin yang melakukan Approve/Reject/Override');
            $table->dateTime('processed_at')->nullable()->comment('Waktu admin melakukan aksi');
            $table->text('rejection_reason')->nullable()->comment('Alasan penolakan atau pembatalan');
            
            // Field Absensi
            $table->string('absent_code', 100)->unique()->nullable();
            $table->string('qr_code_path', 255)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
