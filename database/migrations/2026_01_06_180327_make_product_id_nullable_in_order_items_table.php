<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeProductIdNullableInOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the actual foreign key constraint name
        $constraintName = \DB::select("SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'order_items' 
            AND COLUMN_NAME = 'product_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL")[0]->CONSTRAINT_NAME ?? null;
        
        if ($constraintName) {
            // Drop the existing foreign key constraint
            \DB::statement("ALTER TABLE `order_items` DROP FOREIGN KEY `{$constraintName}`");
        }
        
        // Modify the column to be nullable
        \DB::statement('ALTER TABLE `order_items` MODIFY COLUMN `product_id` BIGINT UNSIGNED NULL');
        
        // Re-add the foreign key constraint with nullable
        \DB::statement('ALTER TABLE `order_items` ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the foreign key constraint
        \DB::statement('ALTER TABLE `order_items` DROP FOREIGN KEY `order_items_product_id_foreign`');
        
        // Make product_id NOT NULL again (first update any NULL values to a default if needed)
        \DB::statement('ALTER TABLE `order_items` MODIFY COLUMN `product_id` BIGINT UNSIGNED NOT NULL');
        
        // Re-add the foreign key constraint
        \DB::statement('ALTER TABLE `order_items` ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE');
    }
}
