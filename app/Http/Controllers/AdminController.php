<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // user dengan unit_id selain mahasiswa biasa 

    public static function dashboard()
    {
        // AUTH login, truws ambil name and unit->name
        return view('admin.dashboard');
    }

    public static function labs()
    {
        $labs = Labs::orderBy('name')->get();

        return view('admin.labs', ['labs' => $labs]);
    }
}
