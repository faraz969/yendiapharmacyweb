<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeUserIdNullableInPrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign key
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        // Modify column using raw SQL
        DB::statement('ALTER TABLE prescriptions MODIFY user_id BIGINT UNSIGNED NULL');
        
        // Re-add foreign key
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        DB::statement('ALTER TABLE prescriptions MODIFY user_id BIGINT UNSIGNED NOT NULL');
        
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}

