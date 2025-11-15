<?php

namespace App\Http\Controllers;

use App\Models\Components;
use Illuminate\Http\Request;

class ComponentsController extends Controller
{
    public function getComponentDetails(Components $component)
    {
        $component->load([
            'type',
            'item',
            'item.desk.lab',
            'specSetValues.specAttributes'
        ]);

        return response()->json($component);
    }

    public function updateComponentCondition(Request $request, Components $component)
    {
        try {
            $component->condition = !$component->condition; // Toggle
            $component->save();

            $newConditionText = $component->condition ? 'Bagus' : 'Rusak';

            return response()->json([
                'success' => true,
                'message' => "Kondisi '{$component->name}' berhasil diubah menjadi '{$newConditionText}'.",
                'new_condition' => $component->condition
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updateComponentCondition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah kondisi komponen.'], 500);
        }
    }
}
