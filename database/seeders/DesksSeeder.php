<?php

namespace Database\Seeders;

use App\Models\Labs;
use App\Models\Desks;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DesksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labs = Labs::all();
        $labPemrograman = $labs->where('name', 'Lab Pemrograman Dasar')->first();
        $labJaringan = $labs->where('name', 'Lab Jaringan Komputer')->first();

        // if (!$labPemrograman || !$labJaringan) {
        //     $this->command->error('Pastikan LabSeeder sudah dijalankan terlebih dahulu!');
        //     return;
        // }

        // Lab Pemrograman Dasar
        $desksPemrograman = [
            ['location' => 'A1', 'wall' => false, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => false, 'condition' => 0],
            ['location' => 'B1', 'wall' => false, 'condition' => 1],
            ['location' => 'B2', 'wall' => true, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
        ];

        // Lab Jaringan Komputer
        $desksJaringan = [
            ['location' => 'A1', 'wall' => false, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => false, 'condition' => 1],
            ['location' => 'A4', 'wall' => false, 'condition' => 1],
            ['location' => 'A5', 'wall' => false, 'condition' => 1],
            ['location' => 'A6', 'wall' => true, 'condition' => 1],
            ['location' => 'A7', 'wall' => false, 'condition' => 1],
            ['location' => 'A8', 'wall' => false, 'condition' => 1],
            ['location' => 'A9', 'wall' => false, 'condition' => 1],
            ['location' => 'A10', 'wall' => false, 'condition' => 1],
            ['location' => 'A11', 'wall' => false, 'condition' => 1],
        ];

        $this->createDesksForLab($labPemrograman, $desksPemrograman);
        $this->createDesksForLab($labJaringan, $desksJaringan);
    }

    private function createDesksForLab(Labs $lab, array $desksData): void
    {
        foreach ($desksData as $desk) {
            Desks::create([
                'id' => Str::uuid(30),
                'lab_id' => $lab->id,
                'location' => $desk['location'],
                'wall' => $desk['wall'],
                'condition' => $desk['condition'],
                'serial_code' => 'DESK-' . strtoupper(Str::random(8)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

}
