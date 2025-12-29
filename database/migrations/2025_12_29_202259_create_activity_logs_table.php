<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // e.g., 'login', 'create_product', 'update_order', etc.
            $table->string('description')->nullable(); // Human-readable description
            $table->string('model_type')->nullable(); // e.g., 'App\Models\Product'
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the related model
            $table->text('properties')->nullable(); // JSON data for additional context
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->text('user_agent')->nullable(); // Browser/client info
            $table->string('url')->nullable(); // The URL where action was performed
            $table->string('method')->nullable(); // HTTP method (GET, POST, etc.)
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('action');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
