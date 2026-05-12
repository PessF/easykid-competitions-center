<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->boolean('is_tax_invoice_requested')->default(false)->after('payment_slip_path');
            $table->string('tax_payer_name')->nullable()->after('is_tax_invoice_requested');
            $table->string('tax_id', 13)->nullable()->after('tax_payer_name');
            $table->string('tax_payer_branch')->nullable()->after('tax_id');
            $table->text('tax_payer_address')->nullable()->after('tax_payer_branch');
            $table->string('tax_payer_phone', 20)->nullable()->after('tax_payer_address');
            $table->string('tax_payer_email')->nullable()->after('tax_payer_phone');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'is_tax_invoice_requested',
                'tax_payer_name',
                'tax_id',
                'tax_payer_branch',
                'tax_payer_address',
                'tax_payer_phone',
                'tax_payer_email'
            ]);
        });
    }
};