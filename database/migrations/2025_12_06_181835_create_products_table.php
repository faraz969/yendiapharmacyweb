<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->text('images')->nullable(); // JSON array of image paths
            $table->string('video')->nullable();
            $table->decimal('selling_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->default(0);
            
            // Unit conversion
            $table->string('purchase_unit')->default('box'); // box, pack, etc.
            $table->string('selling_unit')->default('tablet'); // tablet, capsule, etc.
            $table->integer('conversion_factor')->default(1); // e.g., 1 box = 10 tablets
            
            // Prescription rules
            $table->boolean('requires_prescription')->default(false);
            $table->text('prescription_notes')->nullable();
            
            // Inventory
            $table->integer('min_stock_level')->default(0);
            $table->integer('max_stock_level')->nullable();
            $table->boolean('track_expiry')->default(true);
            $table->boolean('track_batch')->default(true);
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_expired')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
