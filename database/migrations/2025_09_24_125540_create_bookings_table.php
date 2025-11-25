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

            // Khusus Pinjam Lab
            $table->unsignedInteger('attendee_count')->nullable()->comment('Jumlah peserta (jika pinjam ruang)');

            // Untuk Pinjam Item maupun Lab
            $table->string('event_name')->comment('Nama kegiatan/keperluan');
            $table->timestamp('event_started_at');
            $table->timestamp('event_ended_at');
            
            // Khusus Skripsi
            $table->string('thesis_title')->nullable();
            // beserta Relasi Dosen Pembimbing
            $table->uuid('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamp('borrowed_at'); // ini start pinjam barang, untuk lihat kapan apply pinjam maka lihat created_at
            $table->timestamp('return_deadline_at'); // bisa di edit oleh approver
            // jika belum ada returned_at, maka masih dipinjam
            // jika ada returned_at, tapi lebih lambat dari return_deadline_at maka Telat dikembalikan

            $table->boolean('approved')->nullable(); // true di acc untuk pinjam, false ditolak
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('borrower_id');
            $table->foreign('borrower_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('phone_number'); // nomor wa aktif penanggung jawab

            // $table->uuid('returner_id')->nullable();
            // $table->foreign('returner_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('period_id');
            $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade');

            $table->text('booking_detail')->nullable();

            $table->timestamps();
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
