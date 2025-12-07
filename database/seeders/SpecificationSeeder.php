<?php

namespace Database\Seeders;

use App\Models\SpecAttributes;
use App\Models\SpecSetValue;
use App\Models\Items;
use App\Models\Components;
use Illuminate\Database\Seeder;

class SpecificationSeeder extends Seeder
{
    public function run(): void
    {
        // Create basic spec attributes
        $attributes = [
            'BRAND' => ['INTEL', 'AMD', 'NVIDIA', 'CORSAIR', 'SAMSUNG', 'ASUS', 'MSI', 'GIGABYTE'],
            'MODEL' => ['CORE I5', 'CORE I7', 'RYZEN 5', 'RYZEN 7', 'RTX 3060', 'RTX 3070', 'GTX 1660'],
            'CAPACITY' => ['8GB', '16GB', '32GB', '512GB', '1TB', '2TB'],
            'SPEED' => ['2400MHZ', '3200MHZ', '3600MHZ'],
            'RESOLUTION' => ['1920X1080', '2560X1440', '3840X2160'],
            'REFRESH_RATE' => ['60HZ', '75HZ', '144HZ', '165HZ'],
            'SIZE' => ['21 INCH', '24 INCH', '27 INCH', '32 INCH'],
            'TYPE' => ['DDR4', 'DDR5', 'NVME', 'SATA', 'MECHANICAL', 'MEMBRANE'],
        ];

        foreach ($attributes as $attrName => $values) {
            $attribute = SpecAttributes::firstOrCreate(['name' => $attrName]);
            
            foreach ($values as $value) {
                SpecSetValue::firstOrCreate([
                    'spec_attributes_id' => $attribute->id,
                    'value' => $value
                ]);
            }
        }

        // Assign random specifications to items
        $items = Items::all();
        $specValues = SpecSetValue::all();
        
        foreach ($items as $item) {
            // Assign 2-4 random specifications to each item
            $randomSpecs = $specValues->random(rand(2, 4));
            foreach ($randomSpecs as $spec) {
                $item->specSetValues()->syncWithoutDetaching([$spec->id]);
            }
        }

        // Assign random specifications to components
        $components = Components::all();
        
        foreach ($components as $component) {
            // Assign 1-3 random specifications to each component
            $randomSpecs = $specValues->random(rand(1, 3));
            foreach ($randomSpecs as $spec) {
                $component->specSetValues()->syncWithoutDetaching([$spec->id]);
            }
        }
    }
}