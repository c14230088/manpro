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
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();

            $table->string('route');
            $table->enum('action', ['VIEW', 'GET', 'POST', 'PATCH', "DELETE", "PUT"])->default('VIEW');

            $table->uuid('permission_group_id')->nullable();
            $table->foreign('permission_group_id')->references('id')->on('permission_groups')->onDelete('set null'); // instead of hapus, jadikan gapunya group parent saja
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
