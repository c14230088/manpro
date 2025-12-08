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

    public function getAvailableSets(Request $request, Labs $lab)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $requiredTypes = ['Monitor', 'Mouse', 'Keyboard', 'CPU'];
        
        $desks = $lab->desks()->with(['items.type', 'items.repairs'])->get();
        
        $availableDesks = [];
        
        foreach ($desks as $desk) {
            $items = $desk->items;
            
            if ($items->count() < 4) continue;
            
            $typeNames = $items->pluck('type.name')->toArray();
            $hasAllTypes = count(array_intersect($requiredTypes, $typeNames)) === 4;
            
            if (!$hasAllTypes) continue;
            
            $allGoodCondition = $items->every(fn($i) => $i->condition == 1);
            if (!$allGoodCondition) continue;
            
            $anyUnderRepair = $items->some(fn($i) => $i->repairs->isNotEmpty());
            if ($anyUnderRepair) continue;
            
            if ($start && $end) {
                $hasBookingConflict = $items->some(function($item) use ($start, $end) {
                    return DB::table('bookings_items')
                        ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                        ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                        ->where('bookings_items.bookable_id', $item->id)
                        ->where('bookings.borrowed_at', '<', $end)
                        ->where('bookings.return_deadline_at', '>', $start)
                        ->where('bookings.approved', true)
                        ->exists();
                });
                
                if ($hasBookingConflict) continue;
            }
            
            $availableDesks[] = [
                'desk_id' => $desk->id,
                'location' => $desk->location,
                'items' => $items->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                    'type' => $i->type->name
                ])
            ];
        }
        
        return response()->json([
            'available_count' => count($availableDesks),
            'desks' => $availableDesks
        ]);
    }

    public function getDeskMapWithAvailability(Request $request, Labs $lab)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $desks = $lab->desks()
            ->with(['items.type', 'items.repairs', 'items.components.type'])
            ->orderBy('location')
            ->get();

        $deskMap = $desks->map(function($desk) use ($start, $end) {
            $items = $desk->items->map(function($item) use ($start, $end) {
                $isUnderRepair = $item->repairs->isNotEmpty();
                
                $isBooked = false;
                if ($start && $end) {
                    $isBooked = DB::table('bookings_items')
                        ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                        ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                        ->where('bookings_items.bookable_id', $item->id)
                        ->where('bookings.borrowed_at', '<', $end)
                        ->where('bookings.return_deadline_at', '>', $start)
                        ->where('bookings.approved', true)
                        ->exists();
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'serial_code' => $item->serial_code,
                    'type' => $item->type ? $item->type->name : null,
                    'condition' => $item->condition,
                    'available' => $item->condition == 1 && !$isUnderRepair && !$isBooked,
                    'components' => $item->components->map(fn($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'type' => $c->type ? $c->type->name : null
                    ])
                ];
            });

            return [
                'id' => $desk->id,
                'location' => $desk->location,
                'items' => $items
            ];
        });

        return response()->json($deskMap);
    }
}
