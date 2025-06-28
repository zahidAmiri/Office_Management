<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('used_stores', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('unit');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedTinyInteger('condition')->default(5); // 1-5
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_stores');
    }
};
