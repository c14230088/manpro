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
            $table->boolean('condition')->comment('0: rusak | 1: bagus');
            
            // $table->json('additional_information')->nullable(); //spek" yang mau disimpan
            $table->uuid(column: 'spec_set_id');
            $table->foreign('spec_set_id')->references('id')->on('spec_set')->onDelete('cascade');
            
            // $table->uuid('unit_id'); // Components ini milik siapa, UPPK, PTIK atau mhsw
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->uuid('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

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
