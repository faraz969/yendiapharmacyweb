<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('refund_number')->unique();
            $table->decimal('refund_amount', 10, 2);
            $table->enum('refund_method', ['mobile_money', 'bank_transfer'])->default('mobile_money');
            
            // Mobile Money Details
            $table->string('mobile_money_provider')->nullable(); // MTN, Vodafone, AirtelTigo, etc.
            $table->string('mobile_money_number')->nullable();
            $table->string('mobile_money_name')->nullable();
            
            // Bank Account Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_type')->nullable(); // savings, current, etc.
            $table->string('branch_name')->nullable();
            
            $table->enum('status', ['pending', 'approved', 'rejected', 'processed', 'completed'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->string('refund_reference')->nullable(); // Reference from payment gateway
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refund_requests');
    }
};
