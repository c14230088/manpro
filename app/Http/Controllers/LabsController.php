<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabsController extends Controller
{
    public function getLabs()
    {
        $labs = Labs::all(); 
        return response()->json($labs);
    }
    
    public function getDesks(Labs $lab)
    {
        $desks = $lab->desks()
            ->with([
                'items.components.spec.setValues.specAttributes', 
                'items.spec.setValues.specAttributes'
            ])
            ->orderBy('location')
            ->get();
        return response()->json($desks->toArray());
    }
}
