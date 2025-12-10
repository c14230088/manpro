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
            $table->boolean('condition')->comment('0: rusak | 1: baik');

            $table->timestamp('produced_at')->nullable(); // kapan dibuatnya item ini.
            $table->uuid('set_id')->nullable(); // items ini punya keluarga (set) mana.
            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade');
            
            $table->uuid('type_id'); // ->comment('0:monitor | 1: cpu | 2: mouse | 3: keyboard'); // setiap desks harus punya 4 item dengan type-type itu
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');

            $table->uuid('unit_id')->nullable(); // items ini milik siapa, UPPK, PTIK atau mhsw
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

            $table->uuid('desk_id')->nullable(); // bisa bound ke suatu meja, bisa free
            $table->foreign('desk_id')->references('id')->on('desks')->onDelete('cascade');
            
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
        Schema::dropIfExists('items');
    }
};
