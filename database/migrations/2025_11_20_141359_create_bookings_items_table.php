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
        Schema::create('bookings_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('bookable'); // antara item_id atau component_id atau lab_id

            $table->uuid('booking_id');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');

            $table->tinyInteger('type')->comment('0: Onsite | 1: Remote | 2: Keluar Lab');

            $table->uuid('returner_id')->nullable();
            $table->foreign('returner_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('returned_at')->nullable();
            $table->boolean('returned_status')->nullable()->comment('0: Rusak | 1: Normal');
            $table->text('returned_detail')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings_items');
    }
};
