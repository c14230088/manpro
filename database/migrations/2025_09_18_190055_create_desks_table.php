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
        Schema::create('desks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->boolean('wall')->default(false); //buat denah per lab, wall ini antara dinding atau jalan yg ditengah ...
            $table->string('location'); //A1, A2 || XY coordinate
            $table->string('serial_code');
            $table->boolean('condition')->comment('0: rusak | 1: bagus'); // kayaknya ini gausah ... bagusan klo cek dari item di desk ini (jika item di desk ini ada yang rusak -> warning merah || component dari item rusak (RAM, dll) -> warning kuning)

            // $table->uuid('unit_id'); // Meja ini milik siapa, UPPK atau mhsw
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->uuid('lab_id');
            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['location', 'lab_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desks');
    }
};
