<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('batch_number')->unique();
            $table->date('expiry_date');
            $table->date('manufacturing_date')->nullable();
            $table->integer('quantity'); // in purchase unit (e.g., boxes)
            $table->integer('available_quantity'); // remaining quantity
            $table->decimal('cost_price', 10, 2);
            $table->boolean('is_expired')->default(false);
            $table->date('expired_at')->nullable();
            $table->timestamps();
            
            $table->index(['product_id', 'expiry_date']);
            $table->index('is_expired');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batches');
    }
}
