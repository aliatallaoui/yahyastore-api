<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->index();
            $table->string('phone', 20)->nullable()->index();
            $table->json('items');
            $table->unsignedInteger('total')->default(0);
            $table->timestamps();

            $table->unique('session_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('abandoned_carts');
    }
};
