<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Labs;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetController extends Controller
{
    public function index()
    {
        $labs = Labs::orderBy('name')->get();
        $sets = Set::with(['items.type', 'items.desk.lab', 'items.components', 'items.repairs' => function($q) {
            $q->where('repairs_items.status', 1);
        }])->orderBy('name')->get();
        
        $types = \App\Models\Type::orderBy('name')->get();
        $specification = \App\Models\SpecAttributes::with('specValues')->orderBy('name')->get();

        return view('admin.sets', [
            'sets' => $sets,
            'labs' => $labs,
            'types' => $types,
            'specification' => $specification,
        ]);
    }

    public function getSetDetails(Set $set)
    {
        $set->load([
            'items.type',
            'items.desk.lab',
            'items.specSetValues.specAttributes',
            'items.components.type',
            'items.components.specSetValues.specAttributes',
        ]);

        return response()->json($set);
    }

    public function attachSetToDesk(Request $request, Set $set)
    {
        $request->validate([
            'lab_id' => 'required|uuid|exists:labs,id',
            'desk_location' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $items = $set->items()->with('desk')->get();
            
            if ($items->count() !== 4) {
                throw new \Exception('Set harus memiliki 4 item.');
            }

            // foreach ($items as $item) {
            //     if ($item->desk_id) {
            //         throw new \Exception("Item '{$item->name}' sudah terpasang di meja lain.");
            //     }
            // }

            $desk = DB::table('desks')
                ->where('lab_id', $request->lab_id)
                ->where('location', $request->desk_location)
                ->first();

            if (!$desk) {
                throw new \Exception("Meja {$request->desk_location} tidak ditemukan di lab ini.");
            }

            // jika sudah ada MINIMAL 1 saja item dengan TYPE berikut, tidak bisa assign Set ke desk ini (UN-COMMAND INI JIKA MAU BISA ADD SET ke Meja yang berisi)
            // $requiredTypes = ['Monitor', 'Mouse', 'Keyboard', 'CPU'];
            // $existingItems = Items::where('desk_id', $desk->id)
            //     ->with('type')
            //     ->get();

            // foreach ($existingItems as $existing) {
            //     if ($existing->type && in_array($existing->type->name, $requiredTypes)) {
            //         throw new \Exception("Meja {$request->desk_location} sudah memiliki {$existing->type->name}. Tidak bisa memasang set ke meja ini.");
            //     }
            // }

            foreach ($items as $item) {
                $item->desk_id = $desk->id;
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Set '{$set->name}' berhasil dipasang ke meja {$request->desk_location}."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
