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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('book_detail');
            // keys json akan berubah (logic di frontend) : 
            // pinjam ruang = attendee_count 
            // pinjam item = events_name
            // pinjam item && untuk skripsi = thesis_title dan supervisor_name/supervisor_id (dropdown list dosen, tapi harus update kalau ada dosen pembimbing baru)  

            $table->timestamp('borrowed_at');
            $table->timestamp('return_deadline_at')->nullable(); // nullable kah?
            $table->timestamp('returned_at')->nullable();
            // jika belum ada returned_at, maka masih dipinjam
            // jika ada returned_at, tapi lebih lambat dari return_deadline_at maka Telat dikembalikan

            $table->tinyInteger('type')->comment('0: Onsite | 1: Remote');

            $table->boolean('approved')->nullable(); // true di acc untuk pinjam, false ditolak
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');

            $table->uuidMorphs('bookable'); // antara item_id atau component_id atau lab_id

            $table->uuid('borrower_id');
            $table->foreign('borrower_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->uuid('returner_id')->nullable();
            $table->boolean('returned_status')->nullable()->comment('0: Rusak | 1: Normal');            
            $table->foreign('returner_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('period_id');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');

            $table->text('returned_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
