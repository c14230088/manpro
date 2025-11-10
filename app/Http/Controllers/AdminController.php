<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Type;
use App\Models\Items;
use Illuminate\Http\Request;
use App\Models\SpecAttributes;

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

    public function items()
    {
        $items = Items::whereNull('desk_id')
            ->with('type', 'specSetValues.specAttributes')
            ->orderBy('name')
            ->paginate(20);

        $types = Type::orderBy('name')->get();
        $specification = SpecAttributes::with('specValues')->orderBy('name')->get();

        return view('admin.items', [
            'items' => $items,
            'types' => $types,
            'specification' => $specification,
        ]);
    }
}
