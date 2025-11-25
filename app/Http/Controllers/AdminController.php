<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Type;
use App\Models\Items;
use App\Models\Components;
use Illuminate\Http\Request;
use App\Models\SpecAttributes;
use Database\Seeders\specification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class AdminController extends Controller
{
    // user dengan unit_id selain mahasiswa biasa 

    public static function dashboard()
    {
        $admin = Auth::user();
        return view('admin.dashboard', ['name' => $admin->name, 'unit' => $admin->unit->name]);
    }

    public static function labs()
    {
        $labs = Labs::orderBy('name')->get();

        return view('admin.labs', ['labs' => $labs]);
    }

    public function items(Request $request)
{
    $types = Type::orderBy('name')->get();
    $specification = SpecAttributes::with('specValues')->orderBy('name')->get();
    $labs = Labs::orderBy('name')->get();

    $filters = $request->all();

    // 1. Query Items
    // Kita load relasi 'repairs' tapi HANYA yang statusnya 1 (In Progress)
    // Ini untuk memudahkan pengecekan di frontend (Visual Badge)
    $itemsQuery = Items::query()->with([
        'type', 
        'specSetValues.specAttributes', 
        'desk.lab',
        'repairs' => function($q) {
            $q->where('repairs_items.status', 1); 
        }
    ]);

    // Filter Status Pemasangan
    if ($request->filled('status')) {
        if ($request->status == 'unaffiliated') {
            $itemsQuery->whereNull('desk_id');
        } elseif ($request->status == 'affiliated') {
            $itemsQuery->whereNotNull('desk_id');
        }
    }

    // Filter Kondisi (Logic Baru)
    if ($request->filled('condition')) {
        if ($request->condition == 'under_repair') {
            // Filter Item yang sedang diperbaiki
            $itemsQuery->whereHas('repairs', function ($q) {
                $q->where('repairs_items.status', 1);
            });
        } elseif ($request->condition == 'parent_under_repair') {
            // Item tidak punya induk, jadi return kosong agar konsisten
            $itemsQuery->whereRaw('1 = 0'); 
        } else {
            // Filter Bagus/Rusak biasa
            $itemsQuery->where('condition', $request->condition);
        }
    }

    if ($request->filled('type_id')) {
        $itemsQuery->where('type_id', $request->type_id);
    }

    if ($request->filled('lab_id')) {
        $itemsQuery->whereHas('desk', function (Builder $q) use ($request) {
            $q->where('lab_id', $request->lab_id);
        });
    }

    // Filter Specs Item... (Kode lama Anda tetap disini)
    if ($request->filled('spec_attribute_id')) {
        if ($request->filled('spec_value_id')) {
            $itemsQuery->whereHas('specSetValues', function (Builder $q) use ($request) {
                $q->where('spec_set_value.id', $request->spec_value_id);
            });
        } else {
            $itemsQuery->whereHas('specSetValues.specAttributes', function (Builder $q) use ($request) {
                $q->where('spec_set_value.spec_attributes_id', $request->spec_attribute_id);
            });
        }
    }


    // 2. Query Components
    // Load repairs sendiri (status 1) DAN repairs milik item induknya (status 1)
    $componentsQuery = Components::query()->with([
        'type', 
        'specSetValues.specAttributes', 
        'item.desk.lab',
        'repairs' => function($q) {
            $q->where('repairs_items.status', 1);
        },
        'item.repairs' => function($q) { // Load repair induk juga
            $q->where('repairs_items.status', 1);
        }
    ]);

    // Filter Kondisi Components (Logic Baru)
    if ($request->filled('condition')) {
        if ($request->condition == 'under_repair') {
            // Komponen sedang diperbaiki secara langsung
            $componentsQuery->whereHas('repairs', function ($q) {
                $q->where('repairs_items.status', 1);
            });
        } elseif ($request->condition == 'parent_under_repair') {
            // Induk dari komponen sedang diperbaiki
            $componentsQuery->whereHas('item.repairs', function ($q) {
                $q->where('repairs_items.status', 1);
            });
        } else {
            // Bagus/Rusak biasa
            $componentsQuery->where('condition', $request->condition);
        }
    }

    if ($request->filled('type_id')) {
        $componentsQuery->where('type_id', $request->type_id);
    }
    if ($request->filled('lab_id')) {
        $componentsQuery->whereHas('item.desk', function (Builder $q) use ($request) {
            $q->where('lab_id', $request->lab_id);
        });
    }

    // Filter Specs Component... (Kode lama Anda tetap disini)
    if ($request->filled('spec_attribute_id')) {
        if ($request->filled('spec_value_id')) {
            $componentsQuery->whereHas('specSetValues', function (Builder $q) use ($request) {
                $q->where('spec_set_value.id', $request->spec_value_id);
            });
        } else {
            $componentsQuery->whereHas('specSetValues.specAttributes', function (Builder $q) use ($request) {
                $q->where('spec_set_value.spec_attributes_id', $request->spec_attribute_id);
            });
        }
    }

    $items = $itemsQuery->orderBy('name')->get();
    $components = $componentsQuery->orderBy('name')->get();

    return view('admin.items', [
        'items' => $items,
        'components' => $components,
        'types' => $types,
        'specification' => $specification,
        'labs' => $labs,
        'filters' => $filters,
    ]);
}
}
