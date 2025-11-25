<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function formBooking()
    {
        return view('user.booking');
    }

    /**
     * Menyimpan data peminjaman baru.
     * Rute: POST /booking
     */
    public function storeBooking(Request $request)
    {
        // 1. Validasi input dari JavaScript
        $validated = $request->validate([
            'book_detail'   => 'required|json',
            'bookable_id'   => 'required|uuid',
            'bookable_type' => 'required|string',
            'type'          => 'required|integer|in:0,1', // <-- PERUBAHAN: Validasi data 'type'
        ]);

        try {
            // 2. Buat record baru di tabel 'bookings'
            Booking::create([
                'book_detail'   => $validated['book_detail'],
                'bookable_id'   => $validated['bookable_id'],
                'bookable_type' => $validated['bookable_type'],
                'type'          => $validated['type'], // <-- PERUBAHAN: Simpan data 'type'
                
                // Mengisi kolom lain dari migrasi Anda
                'borrower_id' => Auth::id(), 
                'approved'    => null, 
                'borrowed_at' => now(), 
                'period_id'   => '00000000-0000-0000-0000-000000000001'
            ]);

            DB::commit();

            // 3. Kembalikan respons sukses
            return response()->json([
                'message' => 'Permintaan peminjaman Anda telah berhasil diajukan.'
            ], 201); // 201 = Created

        } catch (\Exception $e) {
            // 4. Jika terjadi error
            Log::error('Gagal menyimpan booking: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
