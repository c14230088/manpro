<?php

namespace App\Http\Controllers;

use App\Models\Desks;
use App\Models\Labs;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DesksController extends Controller
{
    public function updateLocation(Request $request, Labs $lab, Desks $desk)
    {
        $request->validate([
            'location' => 'required|string|max:10',
        ]);

        if (!$desk) {
            return response()->json([
                'success' => false,
                'message' => 'Meja tidak ditemukan.'
            ], 404);
        }

        if ($desk->lab_id != $lab->id) {
            return response()->json([
                'success' => false,
                'message' => 'Meja ini bukan milik laboratorium tersebut. Mohon muat ulang halaman ini.'
            ], 403);
        }
        $message = 'Posisi meja berhasil diperbarui dari ' . $desk->location . ' ke ' . $request->input('location');

        $desk->location = $request->input('location');
        $desk->save();

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function batchCreate(Request $request, Labs $lab)
    {
        $validated = $request->validate([
            'desks' => 'required|array',
            'desks.*.location' => 'required|string|max:10',
        ]);

        $createdDesks = [];

        foreach ($validated['desks'] as $deskData) {
            // Logika disesuaikan dengan model Anda
            $newDesk = Desks::create([
                'location' => $deskData['location'],
                'lab_id' => $lab->id,
                'serial_code' => 'TEMP-' . Str::random(8),
                'condition' => 1,
            ]);

            $createdDesks[] = $newDesk->load(['items.components', 'items.spec']);
        }

        return response()->json([
            'success' => true,
            'message' => count($createdDesks) . ' meja baru berhasil ditambahkan.',
            'created_desks' => $createdDesks,
        ]);
    }

    
    public function batchDelete(Request $request, $labId)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'ids'         => 'required|array',
            'ids.*'       => 'string|exists:desks,id',
            'delete_mode' => [ // Validasi mode pilihan
                'required',
                'string',
                Rule::in(['delete_items_only', 'delete_all']), // Harus salah satu
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Input tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $idsOfDesks = $request->input('ids'); // Ini adalah ID Meja
        $deleteMode = $request->input('delete_mode');

        DB::beginTransaction();

        try {
            $message = '';

            // 3. Jalankan logika berdasarkan mode
            if ($deleteMode === 'delete_all') {
                // OPSI 1: Hapus MEJA DAN ITEM
                
                // Hapus item dulu
                Items::whereIn('desk_id', $idsOfDesks)->delete();
                
                // Hapus meja
                $deletedCount = Desks::where('lab_id', $labId)
                                    ->whereIn('id', $idsOfDesks)
                                    ->delete();
                                    
                if ($deletedCount == 0) {
                     DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Tidak ada meja yang ditemukan untuk dihapus.'], 404);
                }
                
                $message = $deletedCount . ' meja dan item terkait berhasil dihapus permanen.';

            } else {
                // OPSI 2: Hapus ITEM SAJA
                
                $itemCount = Items::whereIn('desk_id', $idsOfDesks)->delete();

                // Kita tidak menghapus meja, jadi $deletedCount = 0
                // tapi kita perlu tahu berapa banyak item yang dihapus
                if ($itemCount == 0) {
                     DB::rollBack(); // Rollback jika tidak ada item sama sekali
                    return response()->json(['success' => false, 'message' => 'Tidak ada item yang ditemukan di meja tersebut.'], 404);
                }
                
                // Asumsi: Jika item dihapus, kondisi meja kembali 'bagus'
                Desks::where('lab_id', $labId)
                    ->whereIn('id', $idsOfDesks)
                    ->update(['condition' => 1]);

                $message = $itemCount . ' item dari ' . count($idsOfDesks) . ' berhasil dihapus. Meja tetap ada.';
            }

            // 5. Commit Transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error batch action: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server. ' . $e->getMessage()], 500);
        }
    }
    
}

