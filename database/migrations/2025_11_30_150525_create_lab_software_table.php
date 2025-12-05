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
        Schema::create('lab_softwares', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lab_id');
            $table->uuid('software_id');

            // $table->dateTime('removed_at')->nullable();
            // installed at lihat dari created_at || dan lihat last updated_at jika ada update

            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('cascade');
            $table->foreign('software_id')->references('id')->on('softwares')->onDelete('cascade');
            $table->unique(['lab_id', 'software_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_software');
    }
};
