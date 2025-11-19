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
        Schema::create('repairs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('itemable'); // itemable_type, itemable_id

            $table->uuid('reported_by'); // apakah nullable?
            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->text('issue_description');
            $table->unsignedTinyInteger('status')->default('0')->comment('0: Pending | 1: In Progress | 2: Completed | 3: Terbawa karena Item induk sedang diperbaiki'); // pending, in_progress, completed

            $table->boolean('is_successful')->nullable()->comment('null: belum selesai | true: perbaikan berhasil | false: perbaikan gagal');
            $table->text('repair_notes')->nullable();

            $table->dateTime('reported_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
