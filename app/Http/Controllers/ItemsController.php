<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemsController extends Controller
{
    public function getItems(){
        $items = Items::all();
        return response()->json($items);
    }

    public function getItemsByLab(Labs $lab)
    {
        // Mengambil item yang belum dipinjam, dikelompokkan berdasarkan tipenya, dan dihitung jumlahnya.
        $available_items = Items::where('lab_id', $lab->id)
            ->select('type', DB::raw('count(*) as available_count'))
            ->groupBy('type')
            ->get();

        return response()->json($available_items);
    }
}
