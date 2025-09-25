<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LabsController extends Controller
{
    public function getDesks(Labs $lab)
    {
        $lab->load('desk.item.component');

        $desks = $lab->desk->sortBy('location')->values();

        return response()->json($desks);
    }
}
