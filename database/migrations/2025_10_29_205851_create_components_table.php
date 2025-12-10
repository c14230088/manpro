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
        Schema::create('components', function (Blueprint $table) { // RAM, STORAGE, Graph card, dll
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('serial_code')->unique();
            $table->boolean('condition')->comment('0: rusak | 1: baik');

            $table->timestamp('produced_at')->nullable(); // kapan dibuatnya item ini.

            $table->uuid('type_id');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');

            $table->uuid('unit_id')->nullable(); // Components ini milik siapa, UPPK, PTIK atau mhsw
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->uuid('item_id')->nullable(); // bisa bound ke suatu item, bisa free (misal: stock RAM) 
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->uuid('lab_id')->nullable(); // bisa bound ke suatu meja, bisa free ATAU KE BOUND DI LAB
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
