<?php

namespace Database\Seeders;

use App\Models\Components;
use App\Models\Items;
use App\Models\Type;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ComponentsSeeder extends Seeder
{
    public function run(): void
    {
        $pcItems = Items::whereHas('type', function($q) {
            $q->where('name', 'CPU');
        })->get();
        
        $types = Type::all()->keyBy('name');
        $units = Unit::all();
        
        if ($pcItems->isEmpty() || $types->isEmpty() || $units->isEmpty()) {
            return;
        }

        $ptikUnit = $units->where('name', 'PTIK')->first() ?? $units->first();
        $counter = 1;

        foreach ($pcItems as $pc) {
            // Processor
            Components::create([
                'name' => "PROCESSOR FOR {$pc->name}",
                'serial_code' => "CPU-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'condition' => rand(0, 10) > 1,
                'produced_at' => $pc->produced_at,
                'type_id' => $types['PROCESSOR']->id ?? $types->first()->id,
                'unit_id' => $ptikUnit->id,
                'item_id' => $pc->id,
            ]);

            // RAM
            Components::create([
                'name' => "RAM FOR {$pc->name}",
                'serial_code' => "RAM-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'condition' => rand(0, 10) > 1,
                'produced_at' => $pc->produced_at,
                'type_id' => $types['RAM']->id ?? $types->first()->id,
                'unit_id' => $ptikUnit->id,
                'item_id' => $pc->id,
            ]);

            // Storage
            Components::create([
                'name' => "STORAGE FOR {$pc->name}",
                'serial_code' => "STO-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'condition' => rand(0, 10) > 1,
                'produced_at' => $pc->produced_at,
                'type_id' => $types['STORAGE']->id ?? $types->first()->id,
                'unit_id' => $ptikUnit->id,
                'item_id' => $pc->id,
            ]);
        }
    }
}