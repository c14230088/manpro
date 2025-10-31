<?php

namespace App\Http\Controllers;

use App\Models\Desks;
use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
}
