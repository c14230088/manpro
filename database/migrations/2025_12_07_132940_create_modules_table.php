<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('file_id')->constrained('files')->onDelete('cascade');
            $table->foreignUuid('matkul_id')->constrained('matkuls')->onDelete('cascade');
            $table->foreignUuid('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('workload_hours')->nullable();
            $table->timestamp('last_edited_at')->nullable();
            $table->foreignUuid('last_edited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
