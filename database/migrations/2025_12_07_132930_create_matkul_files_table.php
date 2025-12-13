<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matkul_files', function (Blueprint $table) {
            $table->foreignUuid('matkul_id')->constrained('matkuls')->onDelete('cascade');
            $table->foreignUuid('file_id')->constrained('files')->onDelete('cascade');
            $table->primary(['matkul_id', 'file_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matkul_files');
    }
};
