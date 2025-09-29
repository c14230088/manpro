<?php

namespace Database\Seeders;

use App\Models\Desks;
use App\Models\Items;
use App\Models\Labs;
use App\Models\Unit;
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
                'additional_information' => [
                    'resolution' => '1920x1080',
                    'refresh_rate' => '75Hz'
                ],
            ],
            1 => [
                'name' => 'CPU Lenovo ThinkCentre',
                'additional_information' => [
                    'processor' => 'Intel i5',
                    'ram' => '8GB',
                    'storage' => '512GB SSD'
                ],
            ],
            2 => [
                'name' => 'Mouse Logitech G102',
                'additional_information' => [
                    'dpi' => '800-8000',
                    'color' => 'Black'
                ],
            ],
            3 => [
                'name' => 'Keyboard Redragon Mechanical',
                'additional_information' => [
                    'switch' => 'Blue',
                    'layout' => 'TKL'
                ],
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
                        'additional_information' => json_encode($template['additional_information']),
                        'unit_id' => $units->random()->id,
                        'desk_id' => $desk->id,
                    ]);
                }
            }
        }
    }
}
