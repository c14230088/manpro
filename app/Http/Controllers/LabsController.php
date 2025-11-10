<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabsController extends Controller
{
    public function labsList(){
        $labs = Labs::orderBy('name')->get();

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
