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
use Illuminate\Database\Eloquent\Builder;

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

    public function items(Request $request)
    {
        $types = Type::orderBy('name')->get();
        $specification = SpecAttributes::with('specValues')->orderBy('name')->get();
        $labs = Labs::orderBy('name')->get();

        $filters = $request->all();

        $itemsQuery = Items::query()->with('type', 'specSetValues.specAttributes', 'desk.lab');

        if ($request->filled('status')) { //terpasang ke meja atau tidak
            if ($request->status == 'unaffiliated') {
                $itemsQuery->whereNull('desk_id');
            } elseif ($request->status == 'affiliated') {
                $itemsQuery->whereNotNull('desk_id');
            }
        }

        if ($request->filled('condition')) {
            $itemsQuery->where('condition', $request->condition);
        }

        if ($request->filled('type_id')) {
            $itemsQuery->where('type_id', $request->type_id);
        }

        if ($request->filled('lab_id')) {
            $itemsQuery->whereHas('desk', function (Builder $q) use ($request) {
                $q->where('lab_id', $request->lab_id);
            });
        }

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

        $componentsQuery = Components::query()->with('type', 'specSetValues.specAttributes', 'item.desk.lab');

        if ($request->filled('condition')) {
            $componentsQuery->where('condition', $request->condition);
        }
        if ($request->filled('type_id')) {
            $componentsQuery->where('type_id', $request->type_id);
        }
        if ($request->filled('lab_id')) {
            $componentsQuery->whereHas('item.desk', function (Builder $q) use ($request) {
                $q->where('lab_id', $request->lab_id);
            });
        }
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
