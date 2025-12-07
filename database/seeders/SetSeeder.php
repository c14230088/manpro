<?php

namespace Database\Seeders;

use App\Models\Set;
use App\Models\Type;
use App\Models\Unit;
use App\Models\Items;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class SetSeeder extends Seeder
{
    public function run(): void
    {
        $requiredTypes = ['MONITOR', 'MOUSE', 'KEYBOARD', 'CPU'];
        $types = [];
        
        foreach ($requiredTypes as $typeName) {
            $types[$typeName] = Type::firstOrCreate(['name' => $typeName]);
        }
        
        $unit = Unit::where('name', 'UPPK')->first() ?? Unit::first();
        
        for ($i = 1; $i <= 10; $i++) {
            $set = Set::create([
                'name' => "SET PC-{$i}",
                'note' => "Set komputer lengkap #{$i}",
            ]);
            
            foreach ($requiredTypes as $typeName) {
                Items::create([
                    'name' => "{$typeName} SET-{$i}",
                    'serial_code' => Str::upper(Str::random(3)) . "-{$typeName}-SET{$i}",
                    'condition' => fake()->boolean(85),
                    'produced_at' => fake()->dateTimeBetween('-3 years', 'now'),
                    'set_id' => $set->id,
                    'type_id' => $types[$typeName]->id,
                    'unit_id' => $unit->id,
                ]);
            }
        }
    }
}
