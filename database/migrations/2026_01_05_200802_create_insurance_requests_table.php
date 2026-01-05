<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('insurance_company_id')->constrained()->onDelete('cascade');
            $table->string('request_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('insurance_number');
            $table->string('card_front_image');
            $table->string('card_back_image');
            $table->string('prescription_image')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'order_created'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            // Delivery information (filled when approved)
            $table->foreignId('delivery_address_id')->nullable()->constrained('delivery_addresses')->onDelete('set null');
            $table->foreignId('delivery_zone_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('delivery_type', ['delivery', 'pickup'])->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
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
        Schema::dropIfExists('insurance_requests');
    }
}
