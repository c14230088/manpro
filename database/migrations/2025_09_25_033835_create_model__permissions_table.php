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
        Schema::create('model_permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuidMorphs('model'); // model_type, model_id

            $table->uuid('permission_id');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');

            $table->unique(['model_type', 'model_id', 'permission_id']); // 1 model hanya bisa punya 1 permission yang sama, kalau 1 user ada permission A dan Unit dari user tersebut juga ada permission A maka tidak masalah (jika permission A dihapus dari Unit, maka User masih punya permission A langsung dari dirinya)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_permissions');
    }
};
