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
        Schema::create('spec_set_value', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid(column: 'spec_attributes_id');
            $table->uuid(column: 'spec_set_id');
            $table->foreign('spec_attributes_id')->references('id')->on('spec_attributes')->onDelete('cascade');
            $table->foreign('spec_set_id')->references('id')->on('spec_set')->onDelete('cascade');
            $table->string("value");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spec_set_value');
    }
};
