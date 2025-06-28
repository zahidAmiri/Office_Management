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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('ranking')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('id_number')->unique();
            $table->string('card_number')->unique();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('deleted_at')->nullable();

            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
