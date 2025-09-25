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
        Schema::create('items', function (Blueprint $table) { // MONITOR, SETIR, VR glasses, dll
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('serial_code');
            $table->tinyInteger('type')->comment('0:monitor | 1: cpu | 2: mouse | 3: keyboard'); // setiap desks harus punya 4 item dengan type-type itu
            $table->boolean('condition')->comment('0: rusak | 1: bagus');
            $table->json('additional_information')->nullable(); //spek" yang mau disimpan

            $table->uuid('unit_id'); // items ini milik siapa, UPPK, PTIK atau mhsw
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->uuid('desk_id');
            $table->foreign('desk_id')->references('id')->on('desks')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
