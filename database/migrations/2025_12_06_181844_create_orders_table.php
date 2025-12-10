<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // customer
            $table->unsignedBigInteger('prescription_id')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // pharmacist
            $table->foreignId('packed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('delivered_by')->nullable()->constrained('users')->onDelete('set null'); // delivery person
            $table->unsignedBigInteger('delivery_zone_id')->nullable();
            
            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'packing', 'packed', 'out_for_delivery', 'delivered', 'cancelled'])->default('pending');
            
            // Customer details
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('delivery_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Financial
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Dates
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
            
            $table->index('status');
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
