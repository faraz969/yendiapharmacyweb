<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseOrderForeignKeyToBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
        });
    }
}
