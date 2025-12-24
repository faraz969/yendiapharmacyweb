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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, json, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'header_logo', 'value' => null, 'type' => 'image', 'description' => 'Logo displayed in header', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_logo', 'value' => null, 'type' => 'image', 'description' => 'Logo displayed in footer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'copyright_year', 'value' => date('Y'), 'type' => 'text', 'description' => 'Year displayed in copyright footer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'navbar_categories', 'value' => '[]', 'type' => 'json', 'description' => 'Category IDs to display in navbar', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_store_url', 'value' => null, 'type' => 'url', 'description' => 'Apple App Store URL', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'play_store_url', 'value' => null, 'type' => 'url', 'description' => 'Google Play Store URL', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
