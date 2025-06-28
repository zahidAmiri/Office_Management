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
        Schema::create('experimental_equipments_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('experimental_equipments_transfers')->onDelete('cascade');
            $table->date('return_date');
            $table->unsignedTinyInteger('returned_condition');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experimental_equipments_returns');
    }
};
