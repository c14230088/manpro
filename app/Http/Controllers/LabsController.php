<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabsController extends Controller
{
    public function getDesks(Labs $lab)
    {
        $desks = $lab->desks()
            ->with('items.components')
            ->orderBy('location')
            ->get();

        return response()->json($desks->toArray());
    }
}
