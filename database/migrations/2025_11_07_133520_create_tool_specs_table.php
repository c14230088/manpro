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
        Schema::create('tool_specs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('spec_value_id'); // simpan suatu spec ke sini (1 row di table, berarti 1 spec berupa {key: value} dari suatu item / component)
            $table->foreign('spec_value_id')->references('id')->on('spec_set_value')->onDelete('cascade');

            $table->uuidMorphs('tool'); // ITEMS dan COMPONENTS

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_specs');
    }
};
