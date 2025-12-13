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
        
        // Create 30 sets untuk data yang lebih banyak
        for ($i = 1; $i <= 30; $i++) {
            $set = Set::create([
                'name' => "SET PC-{$i}",
                'note' => "Set komputer lengkap #{$i} - " . fake()->sentence(5),
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
        
        // Create additional sets dengan variasi
        $setVariations = [
            ['name' => 'SET LAPTOP ASUS', 'types' => ['MOUSE', 'KEYBOARD']],
            ['name' => 'SET LAPTOP LENOVO', 'types' => ['MOUSE', 'KEYBOARD']],
            ['name' => 'SET LAPTOP HP', 'types' => ['MOUSE', 'KEYBOARD']],
            ['name' => 'SET GAMING 1', 'types' => ['MONITOR', 'MOUSE', 'KEYBOARD', 'CPU']],
            ['name' => 'SET GAMING 2', 'types' => ['MONITOR', 'MOUSE', 'KEYBOARD', 'CPU']],
        ];
        
        foreach ($setVariations as $variation) {
            $set = Set::create([
                'name' => $variation['name'],
                'note' => "Set khusus untuk " . $variation['name'],
            ]);
            
            foreach ($variation['types'] as $typeName) {
                Items::create([
                    'name' => "{$typeName} {$variation['name']}",
                    'serial_code' => Str::upper(Str::random(3)) . "-{$typeName}-" . Str::random(4),
                    'condition' => fake()->boolean(90),
                    'produced_at' => fake()->dateTimeBetween('-2 years', 'now'),
                    'set_id' => $set->id,
                    'type_id' => $types[$typeName]->id,
                    'unit_id' => $unit->id,
                ]);
            }
        }
    }
}
