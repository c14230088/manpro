<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Components;
use App\Models\SpecSet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComponentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cpuItems = Items::where('type', 1)->get();
        
        if ($cpuItems->isEmpty()) {
            $this->command->warn('⚠️ No CPU items found. Run ItemsSeeder first!');
            return;
        }

        $this->command->info("Found {$cpuItems->count()} CPU items. Seeding components...");

        $componentsCreated = 0;

        $presetCPU_id = SpecSet::where('display_name', 'Intel Core i5 10th Gen')->value('id');
        $presetRAM_id = SpecSet::where('display_name', 'Corsair 8GB DDR4')->value('id');
        $presetSSD_id = SpecSet::where('display_name', 'Samsung 512GB NVMe PCIe 3.0')->value('id');


        foreach ($cpuItems as $cpu) {
            // 1. Processor Component
            Components::create([
                'id' => Str::uuid(),
                'name' => 'Intel Core i5 Processor',
                'serial_code' => 'CPU-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
                'condition' => 1,
                'spec_set_id' => $presetCPU_id,
                'item_id' => $cpu->id,
            ]);
            $componentsCreated++;

            // 2. RAM Component (single 8GB)
            Components::create([
                'id' => Str::uuid(),
                'name' => 'DDR4 8GB RAM',
                'serial_code' => 'RAM-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
                'condition' => 1,
                'spec_set_id' => $presetRAM_id,
                'item_id' => $cpu->id,
            ]);
            $componentsCreated++;

            // 3. Storage Component
            Components::create([
                'id' => Str::uuid(),
                'name' => '512GB SSD',
                'serial_code' => 'SSD-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
                'condition' => 1,
                'spec_set_id' => $presetSSD_id,
                'item_id' => $cpu->id,
            ]);
            $componentsCreated++;
        }
    }
}