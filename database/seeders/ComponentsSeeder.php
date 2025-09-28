<?php

namespace Database\Seeders;

use App\Models\Items;
use App\Models\Components;
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

        foreach ($cpuItems as $cpu) {
            // 1. Processor Component
            Components::create([
                'id' => Str::uuid(),
                'name' => 'Intel Core i5 Processor',
                'serial_code' => 'CPU-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
                'condition' => 1,
                'additional_information' => json_encode([
                    'brand' => 'Intel',
                    'model' => 'Core i5',
                    'cores' => '6 cores',
                    'threads' => '12 threads',
                    'base_clock' => '2.9GHz',
                    'boost_clock' => '4.1GHz',
                    'generation' => '10th Gen'
                ]),
                'item_id' => $cpu->id,
            ]);
            $componentsCreated++;

            // 2. RAM Component (single 8GB)
            Components::create([
                'id' => Str::uuid(),
                'name' => 'DDR4 8GB RAM',
                'serial_code' => 'RAM-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
                'condition' => 1,
                'additional_information' => json_encode([
                    'type' => 'DDR4',
                    'capacity' => '8GB',
                    'speed' => '2666MHz',
                    'brand' => 'Corsair',
                    'latency' => 'CL16'
                ]),
                'item_id' => $cpu->id,
            ]);
            $componentsCreated++;

            // 3. Storage Component
            Components::create([
                'id' => Str::uuid(),
                'name' => '512GB SSD',
                'serial_code' => 'SSD-' . strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(6)),
                'condition' => 1,
                'additional_information' => json_encode([
                    'type' => 'NVMe SSD',
                    'capacity' => '512GB',
                    'interface' => 'PCIe 3.0',
                    'brand' => 'Samsung'
                ]),
                'item_id' => $cpu->id,
            ]);
            $componentsCreated++;
        }
    }
}