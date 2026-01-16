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
        Schema::table('notifications', function (Blueprint $table) {
            $table->boolean('for_admin')->default(false)->after('user_id');
            $table->foreignId('insurance_request_id')->nullable()->after('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('refund_request_id')->nullable()->after('insurance_request_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['insurance_request_id']);
            $table->dropForeign(['refund_request_id']);
            $table->dropColumn(['for_admin', 'insurance_request_id', 'refund_request_id']);
        });
    }
};
