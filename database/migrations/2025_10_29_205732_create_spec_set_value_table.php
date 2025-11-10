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
        Schema::create('spec_set_value', function (Blueprint $table) { // value dari attribute, tiap attribute bisa punya banyak value
            $table->uuid("id")->primary();
            $table->string("value");
            
            $table->uuid(column: 'spec_attributes_id');
            $table->foreign('spec_attributes_id')->references('id')->on('spec_attributes')->onDelete('cascade');
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
