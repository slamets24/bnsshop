<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('xendit_invoice_id')->nullable()->after('note');
            $table->string('payment_method')->nullable()->after('xendit_invoice_id');
            $table->string('payment_channel')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_channel');
            $table->enum('order_type', ['online', 'offline'])->default('online')->after('paid_at');
            $table->foreignId('created_by')->nullable()->after('order_type')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'xendit_invoice_id',
                'payment_method',
                'payment_channel',
                'paid_at',
                'order_type',
                'created_by'
            ]);
        });
    }
};
