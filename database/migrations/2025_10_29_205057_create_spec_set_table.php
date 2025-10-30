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
        Schema::create('spec_set', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid(column: 'spec_type_id');
            $table->foreign('spec_type_id')->references('id')->on('spec_type')->onDelete('cascade');
            $table->string("display_name");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_set');
    }
};
