<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Labs;
use App\Models\Type;
use App\Models\Set;
use App\Models\Unit;
use App\Models\Desks;
use Illuminate\Database\Seeder;

class ItemsSeeder extends Seeder
{
    public function run(): void
    {
        $labs = Labs::all();
        $types = Type::all()->keyBy('name');
        $sets = Set::all();
        $units = Unit::all();
        
        if ($labs->isEmpty() || $types->isEmpty() || $units->isEmpty()) {
            return;
        }

        $ptikUnit = $units->where('name', 'PTIK')->first() ?? $units->first();
        $counter = 1;

        foreach ($labs as $lab) {
            $desks = Desks::where('lab_id', $lab->id)->get();

            foreach ($desks as $desk) {
                // Monitor
                Items::create([
                    'name' => "MONITOR {$lab->name} {$desk->location}",
                    'serial_code' => "MON-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                    'condition' => rand(0, 10) > 1,
                    'produced_at' => now()->subMonths(rand(6, 36)),
                    'type_id' => $types['MONITOR']->id ?? $types->first()->id,
                    'unit_id' => $ptikUnit->id,
                    'lab_id' => $lab->id,
                    'desk_id' => $desk->id,
                ]);

                // CPU
                Items::create([
                    'name' => "CPU {$lab->name} {$desk->location}",
                    'serial_code' => "CPU-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                    'condition' => rand(0, 10) > 1,
                    'produced_at' => now()->subMonths(rand(6, 36)),
                    'type_id' => $types['CPU']->id ?? $types->first()->id,
                    'unit_id' => $ptikUnit->id,
                    'lab_id' => $lab->id,
                    'desk_id' => $desk->id,
                ]);

                // Keyboard
                Items::create([
                    'name' => "KEYBOARD {$lab->name} {$desk->location}",
                    'serial_code' => "KB-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                    'condition' => rand(0, 10) > 1,
                    'produced_at' => now()->subMonths(rand(6, 36)),
                    'type_id' => $types['KEYBOARD']->id ?? $types->first()->id,
                    'unit_id' => $ptikUnit->id,
                    'lab_id' => $lab->id,
                    'desk_id' => $desk->id,
                ]);

                // Mouse
                Items::create([
                    'name' => "MOUSE {$lab->name} {$desk->location}",
                    'serial_code' => "MS-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                    'condition' => rand(0, 10) > 1,
                    'produced_at' => now()->subMonths(rand(6, 36)),
                    'type_id' => $types['MOUSE']->id ?? $types->first()->id,
                    'unit_id' => $ptikUnit->id,
                    'lab_id' => $lab->id,
                    'desk_id' => $desk->id,
                ]);
            }

            // VR headsets for VR lab
            if (stripos($lab->name, 'VR') !== false || stripos($lab->name, 'Virtual') !== false) {
                for ($i = 1; $i <= 3; $i++) {
                    Items::create([
                        'name' => "VR HEADSET {$lab->name} #{$i}",
                        'serial_code' => "VR-" . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                        'condition' => rand(0, 10) > 2,
                        'produced_at' => now()->subMonths(rand(3, 18)),
                        'type_id' => $types['VR HEADSET']->id ?? $types->first()->id,
                        'unit_id' => $ptikUnit->id,
                        'lab_id' => $lab->id,
                        'desk_id' => null,
                    ]);
                }
            }
        }
    }
}