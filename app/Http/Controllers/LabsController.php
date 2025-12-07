<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LabsController extends Controller
{
    public function labsList(Request $request)
    {
        // Ambil parameter untuk cek bentrok jadwal (opsional, tapi disarankan tetap ada)
        $start = $request->query('start');
        $end   = $request->query('end');

        $labs = Labs::orderBy('name')->get();

        // Mapping untuk mengirimkan data stok ke Frontend
        $labs = $labs->map(function ($lab) {
            // CONTOH LOGIKA STOK (Sesuaikan dengan nama kolom di DB Anda)
            // Di sini saya asumsikan tabel labs punya kolom qty_monitor, qty_pc, dll.
            // Atau Anda bisa ambil dari relasi inventory.

            return [
                'id' => $lab->id,
                'name' => $lab->name,
                'inventory' => [
                    'monitor'  => $lab->qty_monitor ?? 20, // Default 20 jika kolom null
                    'pc'       => $lab->qty_pc ?? 20,
                    'keyboard' => $lab->qty_keyboard ?? 20,
                    'mouse'    => $lab->qty_mouse ?? 20,
                ]
            ];
        });

        return response()->json($labs);
    }

    public function getDesks(Labs $lab)
    {
        $desks = $lab->desks()
            ->with([
                'items.type',
                'items.specSetValues.specAttributes',
                'items.components.type',
                'items.components.specSetValues.specAttributes',
            ])
            ->orderBy('location')
            ->get();
        return response()->json($desks->toArray());
    }
}
