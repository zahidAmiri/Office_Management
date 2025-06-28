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
        Schema::create('experimental_equipments_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_type');
            $table->string('unit');
            $table->integer('quantity');
            $table->foreignId('recipient_id')->constrained('employees');
            $table->foreignId('distributed_by')->constrained('employees');
            $table->foreignId('department_id')->constrained('departments');
            $table->date('date');
            $table->unsignedTinyInteger('product_condition'); // 1-5
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experimental_equipments_transfers');
    }
};
