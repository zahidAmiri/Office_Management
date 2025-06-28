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
        Schema::table('experimental_equipments_transfers', function (Blueprint $table) {
            $table->string('status')->default('In Working')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experimental_equipments_transfers', function (Blueprint $table) {
            //
        });
    }
};
