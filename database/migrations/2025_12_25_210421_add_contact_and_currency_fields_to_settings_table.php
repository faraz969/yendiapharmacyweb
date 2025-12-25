<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert new settings
        DB::table('settings')->insert([
            ['key' => 'contact_phone', 'value' => '+1 800 900', 'type' => 'text', 'description' => 'Contact phone number displayed in topbar', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'contact_email', 'value' => 'info@pharmacystore.com', 'type' => 'email', 'description' => 'Contact email address', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'topbar_tagline', 'value' => 'Super Value Deals - Save more with coupons', 'type' => 'text', 'description' => 'Tagline displayed in top utility bar', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'USD', 'type' => 'text', 'description' => 'Currency code (USD, NGN, EUR, etc.)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'text', 'description' => 'Currency symbol ($, ₦, €, etc.)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['contact_phone', 'contact_email', 'topbar_tagline', 'currency', 'currency_symbol'])->delete();
    }
};
