<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('value')->unique();
            $table->enum('type', ['purchase', 'selling', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        DB::table('product_units')->insert([
            ['name' => 'Box', 'value' => 'box', 'type' => 'purchase', 'is_active' => true, 'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pack', 'value' => 'pack', 'type' => 'purchase', 'is_active' => true, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Bottle', 'value' => 'bottle', 'type' => 'purchase', 'is_active' => true, 'sort_order' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Piece', 'value' => 'piece', 'type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tablet', 'value' => 'tablet', 'type' => 'selling', 'is_active' => true, 'sort_order' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Capsule', 'value' => 'capsule', 'type' => 'selling', 'is_active' => true, 'sort_order' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ML', 'value' => 'ml', 'type' => 'selling', 'is_active' => true, 'sort_order' => 7, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('product_units');
    }
};
