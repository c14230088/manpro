<?php

namespace Database\Seeders;

use App\Models\Desks;
use App\Models\Items;
use App\Models\Labs;
use App\Models\Unit;
use App\Models\SpecSet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $units = Unit::all();
        if ($units->isEmpty()) {
            $this->command->warn('⚠️ Units table kosong. Jalankan UnitsSeeder dulu!');
            return;
        }

        $presetMonitor_id = SpecSet::where('display_name', '1920x1080 75Hz')->value('id');
        $presetMouse_id = SpecSet::where('display_name', 'Dpi 800-8000 Black')->value('id');
        $presetKeyboard_id = SpecSet::where('display_name', 'TKL Blue')->value('id');


        // Konfigurasi per lab
        $config = [
            'Lab Pemrograman Dasar' => [
                'desks' => 5, // jumlah desks yg mau di isi
                'items' => [0, 1, 2, 3],
            ],
            'Lab Studio' => [
                'desks' => 5,
                'items' => [0, 1, 2, 3],
            ],
            'Lab Sistem Informasi' => [
                'desks' => 5,
                'items' => [0, 1, 2, 3],
            ],

            'Lab Multimedia' => [
                'desks' => 5,
                'items' => [0, 1, 2, 3],
            ],

            'Lab Jaringan Komputer' => [
                'desks' => 5,
                'items' => [0, 1, 2, 3],
            ],

            'Lab Sistem Cerdas' => [
                'desks' => 5,
                'items' => [0, 1, 2, 3],
            ],

            'Lab Virtual Reality' => [
                'desks' => 5,
                'items' => [0, 1, 2, 3],
            ],
        ];

        $itemTemplates = [
            0 => [
                'name' => 'Monitor LG 24 inch',
                'spec_set_id' => $presetMonitor_id,
            ],
            1 => [
                'name' => 'CPU Lenovo ThinkCentre',
                'spec_set_id' => null,
            ],
            2 => [
                'name' => 'Mouse Logitech G102',
                'spec_set_id' => $presetMouse_id,
            ],
            3 => [
                'name' => 'Keyboard Redragon Mechanical',
                'spec_set_id' => $presetKeyboard_id,
            ],
        ];

        foreach ($config as $labName => $rules) {
            $lab = Labs::where('name', $labName)->first();
            if (!$lab) {
                $this->command->warn("⚠️ Lab {$labName} tidak ditemukan, skip...");
                continue;
            }

            // Ambil desk sesuai aturan
            $desks = Desks::where('lab_id', $lab->id)->take($rules['desks'])->get();

            foreach ($desks as $desk) {
                foreach ($rules['items'] as $type) {
                    $template = $itemTemplates[$type];

                    Items::create([
                        'id' => Str::uuid(30),
                        'name' => $template['name'],
                        'serial_code' => strtoupper(substr($template['name'], 0, 3)) . '-' . strtoupper(Str::random(6)),
                        'type' => $type,
                        'condition' => 1,
                        'spec_set_id' => $template['spec_set_id'],
                        'unit_id' => $units->random()->id,
                        'desk_id' => $desk->id,
                    ]);
                }
            }
        }
    }
}
