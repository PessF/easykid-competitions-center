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
        $table->string('regis_no')->unique();
        
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('team_id')->constrained()->cascadeOnDelete();
        $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
        $table->foreignId('competition_class_id')->constrained('competition_classes')->cascadeOnDelete();

        $table->unsignedBigInteger('payment_transaction_id')->nullable()->after('competition_class_id');
        
        $table->enum('status', ['pending_payment', 'waiting_verify', 'approved', 'rejected'])->default('pending_payment');
        $table->timestamp('checked_in_at')->nullable()->after('status');
        $table->timestamps();

        $table->index(['status', 'competition_class_id']); 
        $table->index('payment_transaction_id');
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