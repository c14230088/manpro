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
        Schema::create('folders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name');
            $table->uuid('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('folders')->nullOnDelete();

            $table->string('full_path')->unique();
            $table->boolean('open_public')->default(false);

            $table->foreignUuid('owner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->unique(['name', 'parent_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
