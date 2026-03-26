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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('regis_no')->unique(); // รหัสใบสมัคร เช่น REG-20260322-0001
            
            // Foreign Keys
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competition_class_id')->constrained('competition_classes')->cascadeOnDelete();
            
            // สถานะและการชำระเงิน
            $table->enum('status', ['pending_payment', 'waiting_verify', 'approved', 'rejected'])->default('pending_payment');
            $table->string('payment_slip_path')->nullable(); // เก็บ Path Google Drive
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
