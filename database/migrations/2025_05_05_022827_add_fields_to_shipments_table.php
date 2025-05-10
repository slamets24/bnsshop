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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('courier')->nullable()->after('courier_name');
            $table->decimal('shipping_cost', 20, 2)->nullable()->after('courier');
            $table->timestamp('estimated_delivery')->nullable()->after('shipping_cost');
            $table->text('note')->nullable()->after('estimated_delivery');
            $table->foreignId('created_by')->nullable()->after('note')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['courier', 'shipping_cost', 'estimated_delivery', 'note', 'created_by']);
        });
    }
};
