<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event', 50);
            $table->string('session_id', 64)->nullable();
            $table->string('page', 200)->nullable();
            $table->string('product_id', 100)->nullable();
            $table->string('product_name', 200)->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('ua', 300)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['event', 'created_at']);
            $table->index('session_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('analytics_events');
    }
};
