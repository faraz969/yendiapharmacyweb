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
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->string('recipient_code')->nullable()->after('refund_reference');
            $table->string('transfer_code')->nullable()->after('recipient_code');
            $table->string('bank_code')->nullable()->after('transfer_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refund_requests', function (Blueprint $table) {
            $table->dropColumn(['recipient_code', 'transfer_code', 'bank_code']);
        });
    }
};
