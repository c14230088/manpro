<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Components;
use App\Models\SpecSetValue;
use App\Models\Tool_spec;
use Illuminate\Database\Seeder;

class ToolSpecSeeder extends Seeder
{
    public function run(): void
    {
        $specValues = SpecSetValue::all();
        if ($specValues->isEmpty()) return;

        // Attach specs to items
        Items::chunk(50, function($items) use ($specValues) {
            foreach ($items as $item) {
                $randomSpecs = $specValues->random(rand(2, 4));
                foreach ($randomSpecs as $spec) {
                    Tool_spec::firstOrCreate([
                        'tool_type' => 'App\Models\Items',
                        'tool_id' => $item->id,
                        'spec_value_id' => $spec->id,
                    ]);
                }
            }
        });

        // Attach specs to components
        Components::chunk(50, function($components) use ($specValues) {
            foreach ($components as $component) {
                $randomSpecs = $specValues->random(rand(1, 3));
                foreach ($randomSpecs as $spec) {
                    Tool_spec::firstOrCreate([
                        'tool_type' => 'App\Models\Components',
                        'tool_id' => $component->id,
                        'spec_value_id' => $spec->id,
                    ]);
                }
            }
        });
    }
}
