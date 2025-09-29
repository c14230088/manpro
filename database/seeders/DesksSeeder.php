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
        $labStudio = $labs->where('name', 'Lab Studio')->first();
        $labSI = $labs->where('name', 'Lab Sistem Informasi')->first();
        $labSC = $labs->where('name', 'Lab Sistem Cerdas')->first();
        $labMM = $labs->where('name', 'Lab Multimedia')->first();
        $labVR = $labs->where('name', 'Lab Virtual Reality')->first();

        // if (!$labPemrograman || !$labJaringan) {
        //     $this->command->error('Pastikan LabSeeder sudah dijalankan terlebih dahulu!');
        //     return;
        // }

        // Lab Pemrograman Dasar
        $desksPemrograman = [
            ['location' => 'A2',  'condition' => 1],

            ['location' => 'B1',  'condition' => 1],
            ['location' => 'B2',  'condition' => 1],
            ['location' => 'B3',  'condition' => 1],
            ['location' => 'B4',  'condition' => 1],
            ['location' => 'B6',  'condition' => 1],
            ['location' => 'B7',  'condition' => 1],
            ['location' => 'B8',  'condition' => 1],

            ['location' => 'C1',  'condition' => 1],
            ['location' => 'C2',  'condition' => 1],
            ['location' => 'C3',  'condition' => 1],
            ['location' => 'C4',  'condition' => 1],
            ['location' => 'C6',  'condition' => 1],
            ['location' => 'C7',  'condition' => 1],
            ['location' => 'C8',  'condition' => 1],

            ['location' => 'D1',  'condition' => 1],
            ['location' => 'D2',  'condition' => 1],
            ['location' => 'D3',  'condition' => 1],
            ['location' => 'D4',  'condition' => 1],
            ['location' => 'D6',  'condition' => 1],
            ['location' => 'D7',  'condition' => 1],
            ['location' => 'D8',  'condition' => 1],

            ['location' => 'E2',  'condition' => 1],
            ['location' => 'E3',  'condition' => 1],
            ['location' => 'E4',  'condition' => 1],
            ['location' => 'E6',  'condition' => 1],
            ['location' => 'E7',  'condition' => 1],

            ['location' => 'F1',  'condition' => 1],
            ['location' => 'F2',  'condition' => 1],
            ['location' => 'F3',  'condition' => 1],
            ['location' => 'F4',  'condition' => 1],
            ['location' => 'F6',  'condition' => 1],
            ['location' => 'F7',  'condition' => 1],
            ['location' => 'F8',  'condition' => 1],

            ['location' => 'G1',  'condition' => 1],
            ['location' => 'G2',  'condition' => 1],
            ['location' => 'G3',  'condition' => 1],
            ['location' => 'G4',  'condition' => 1],
            ['location' => 'G6',  'condition' => 1],
            ['location' => 'G7',  'condition' => 1],
            ['location' => 'G8',  'condition' => 1],

            ['location' => 'H1',  'condition' => 1],
            ['location' => 'H2',  'condition' => 1],
            ['location' => 'H3',  'condition' => 1],
            ['location' => 'H4',  'condition' => 1],

        ];

        // Lab Jaringan Komputer
        $desksJaringan = [
            ['location' => 'A2',  'condition' => 1],

            ['location' => 'B2',  'condition' => 1],
            ['location' => 'B3',  'condition' => 1],
            ['location' => 'B4',  'condition' => 1],
            ['location' => 'B5',  'condition' => 1],
            ['location' => 'B6',  'condition' => 1],
            ['location' => 'B7',  'condition' => 1],
            ['location' => 'B8',  'condition' => 1],

            ['location' => 'C2',  'condition' => 1],
            ['location' => 'C3',  'condition' => 1],
            ['location' => 'C4',  'condition' => 1],
            ['location' => 'C5',  'condition' => 1],
            ['location' => 'C6',  'condition' => 1],
            ['location' => 'C7',  'condition' => 1],
            ['location' => 'C8',  'condition' => 1],

            ['location' => 'D2',  'condition' => 1],
            ['location' => 'D3',  'condition' => 1],
            ['location' => 'D4',  'condition' => 1],
            ['location' => 'D5',  'condition' => 1],
            ['location' => 'D6',  'condition' => 1],
            ['location' => 'D7',  'condition' => 1],
            ['location' => 'D8',  'condition' => 1],

        ];

        // Lab Studio
        $desksStudio = [
            ['location' => 'A2',  'condition' => 1],

            ['location' => 'B1',  'condition' => 1],
            ['location' => 'B2',  'condition' => 1],
            ['location' => 'B3',  'condition' => 1],
            ['location' => 'B5',  'condition' => 1],
            ['location' => 'B6',  'condition' => 1],
            ['location' => 'B7',  'condition' => 1],


            ['location' => 'D1',  'condition' => 1],
            ['location' => 'D2',  'condition' => 1],
            ['location' => 'D3',  'condition' => 1],
            ['location' => 'D5',  'condition' => 1],
            ['location' => 'D6',  'condition' => 1],

            ['location' => 'E1',  'condition' => 1],
            ['location' => 'E2',  'condition' => 1],
            ['location' => 'E3',  'condition' => 1],
            ['location' => 'E5',  'condition' => 1],
            ['location' => 'E6',  'condition' => 1],


            ['location' => 'G1',  'condition' => 1],
            ['location' => 'G2',  'condition' => 1],
            ['location' => 'G3',  'condition' => 1],
            ['location' => 'G5',  'condition' => 1],
            ['location' => 'G6',  'condition' => 1],
            ['location' => 'G7',  'condition' => 1],

            ['location' => 'H1',  'condition' => 1],
            ['location' => 'H2',  'condition' => 1],
            ['location' => 'H3',  'condition' => 1],
            ['location' => 'H5',  'condition' => 1],
            ['location' => 'H6',  'condition' => 1],
            ['location' => 'H7',  'condition' => 1],


            ['location' => 'J1',  'condition' => 1],
            ['location' => 'J2',  'condition' => 1],
            ['location' => 'J3',  'condition' => 1],
            ['location' => 'J5',  'condition' => 1],
            ['location' => 'J6',  'condition' => 1],
            ['location' => 'J7',  'condition' => 1],

            ['location' => 'K1',  'condition' => 1],
            ['location' => 'K2',  'condition' => 1],
            ['location' => 'K3',  'condition' => 1],
            ['location' => 'K5',  'condition' => 1],
            ['location' => 'K6',  'condition' => 1],
            ['location' => 'K7',  'condition' => 1],

        ];

        $desksSI = [

            ['location' => 'A6',  'condition' => 1],

            ['location' => 'B1',  'condition' => 1],
            ['location' => 'B2',  'condition' => 1],
            ['location' => 'B3',  'condition' => 1],
            ['location' => 'B4',  'condition' => 1],
            ['location' => 'B5',  'condition' => 1],
            ['location' => 'B6',  'condition' => 1],
            ['location' => 'B7',  'condition' => 1],
            ['location' => 'B9',  'condition' => 1],
            ['location' => 'B10',  'condition' => 1],
            ['location' => 'B11',  'condition' => 1],
            ['location' => 'B12',  'condition' => 1],
            ['location' => 'B13',  'condition' => 1],
            ['location' => 'B14',  'condition' => 1],
            ['location' => 'B15',  'condition' => 1],

            ['location' => 'C1',  'condition' => 1],
            ['location' => 'C2',  'condition' => 1],
            ['location' => 'C3',  'condition' => 1],
            ['location' => 'C4',  'condition' => 1],
            ['location' => 'C5',  'condition' => 1],
            ['location' => 'C6',  'condition' => 1],
            ['location' => 'C7',  'condition' => 1],
            ['location' => 'C9',  'condition' => 1],
            ['location' => 'C10',  'condition' => 0],
            ['location' => 'C11',  'condition' => 1],
            ['location' => 'C12',  'condition' => 1],
            ['location' => 'C13',  'condition' => 1],
            ['location' => 'C14',  'condition' => 1],
            ['location' => 'C15',  'condition' => 1],

            ['location' => 'D1',  'condition' => 1],
            ['location' => 'D2',  'condition' => 1],
            ['location' => 'D3',  'condition' => 1],
            ['location' => 'D4',  'condition' => 1],
            ['location' => 'D5',  'condition' => 1],
            ['location' => 'D6',  'condition' => 1],
            ['location' => 'D7',  'condition' => 1],
            ['location' => 'D9',  'condition' => 1],
            ['location' => 'D10',  'condition' => 1],
            ['location' => 'D11',  'condition' => 1],
            ['location' => 'D12',  'condition' => 1],
            ['location' => 'D13',  'condition' => 1],
            ['location' => 'D14',  'condition' => 1],
            ['location' => 'D15',  'condition' => 1],

            ['location' => 'E1',  'condition' => 1],
            ['location' => 'E2',  'condition' => 1],
            ['location' => 'E3',  'condition' => 1],
            ['location' => 'E4',  'condition' => 1],
            ['location' => 'E5',  'condition' => 1],
            ['location' => 'E6',  'condition' => 1],
            ['location' => 'E7',  'condition' => 1],
            ['location' => 'E9',  'condition' => 1],
            ['location' => 'E10',  'condition' => 1],
            ['location' => 'E11',  'condition' => 1],
            ['location' => 'E12',  'condition' => 1],
            ['location' => 'E13',  'condition' => 1],
            ['location' => 'E14',  'condition' => 1],
            ['location' => 'E15',  'condition' => 1],

        ];

        $desksSC = [

            ['location' => 'A3',  'condition' => 1],

            ['location' => 'B1',  'condition' => 1],
            ['location' => 'B2',  'condition' => 1],
            ['location' => 'B3',  'condition' => 1],
            ['location' => 'B4',  'condition' => 1],
            ['location' => 'B5',  'condition' => 1],
            ['location' => 'B6',  'condition' => 1],

            ['location' => 'C1',  'condition' => 1],
            ['location' => 'C2',  'condition' => 1],
            ['location' => 'C3',  'condition' => 1],
            ['location' => 'C4',  'condition' => 1],
            ['location' => 'C5',  'condition' => 1],
            ['location' => 'C6',  'condition' => 1],

            ['location' => 'D1',  'condition' => 1],
            ['location' => 'D2',  'condition' => 1],
            ['location' => 'D3',  'condition' => 1],
            ['location' => 'D4',  'condition' => 1],
            ['location' => 'D5',  'condition' => 1],
            ['location' => 'D6',  'condition' => 1],

            ['location' => 'E1',  'condition' => 1],
            ['location' => 'E2',  'condition' => 1],
            ['location' => 'E3',  'condition' => 1],
            ['location' => 'E4',  'condition' => 1],
            ['location' => 'E5',  'condition' => 1],
            ['location' => 'E6',  'condition' => 1],

            ['location' => 'F1',  'condition' => 1],
            ['location' => 'F2',  'condition' => 1],
            ['location' => 'F3',  'condition' => 1],
            ['location' => 'F4',  'condition' => 1],
            ['location' => 'F5',  'condition' => 1],
            ['location' => 'F6',  'condition' => 1],

            ['location' => 'G1',  'condition' => 1],
            ['location' => 'G2',  'condition' => 1],
            ['location' => 'G3',  'condition' => 1],
            ['location' => 'G4',  'condition' => 1],
            ['location' => 'G5',  'condition' => 1],
            ['location' => 'G6',  'condition' => 1],

        ];

        $desksMM = [

            ['location' => 'A1',  'condition' => 1],
            ['location' => 'A2',  'condition' => 1],
            ['location' => 'A6',  'condition' => 1],

            ['location' => 'B1',  'condition' => 1],
            ['location' => 'B2',  'condition' => 1],
            ['location' => 'B3',  'condition' => 1],
            ['location' => 'B4',  'condition' => 1],
            ['location' => 'B6',  'condition' => 1],
            ['location' => 'B7',  'condition' => 1],
            ['location' => 'B8',  'condition' => 1],

            ['location' => 'C1',  'condition' => 1],
            ['location' => 'C2',  'condition' => 1],
            ['location' => 'C3',  'condition' => 1],
            ['location' => 'C4',  'condition' => 1],
            ['location' => 'C6',  'condition' => 1],
            ['location' => 'C7',  'condition' => 1],
            ['location' => 'C8',  'condition' => 1],

            ['location' => 'D1',  'condition' => 1],
            ['location' => 'D2',  'condition' => 1],
            ['location' => 'D3',  'condition' => 1],
            ['location' => 'D4',  'condition' => 1],
            ['location' => 'D6',  'condition' => 1],
            ['location' => 'D7',  'condition' => 1],
            ['location' => 'D8',  'condition' => 1],

        ];

        $desksVR = [

            ['location' => 'A1',  'condition' => 1],
            ['location' => 'A2',  'condition' => 1],
            ['location' => 'A8',  'condition' => 1],

            ['location' => 'B6',  'condition' => 1],
            ['location' => 'B7',  'condition' => 1],
            ['location' => 'B8',  'condition' => 1],

            ['location' => 'C6',  'condition' => 1],
            ['location' => 'C7',  'condition' => 1],
            ['location' => 'C8',  'condition' => 1],

            ['location' => 'D1',  'condition' => 1],

            ['location' => 'E1',  'condition' => 1],
            ['location' => 'E8',  'condition' => 1],

            ['location' => 'F1',  'condition' => 1],
            ['location' => 'F8',  'condition' => 1],

            ['location' => 'G1',  'condition' => 1],
            ['location' => 'G8',  'condition' => 1],

        ];



        $this->createDesksForLab($labPemrograman, $desksPemrograman);
        $this->createDesksForLab($labJaringan, $desksJaringan);
        $this->createDesksForLab($labStudio, $desksStudio);
        $this->createDesksForLab($labSI, $desksSI);
        $this->createDesksForLab($labSC, $desksSC);
        $this->createDesksForLab($labMM, $desksMM);
        $this->createDesksForLab($labVR, $desksVR);
    }

    private function createDesksForLab(Labs $lab, array $desksData): void
    {
        foreach ($desksData as $desk) {
            Desks::create([
                'id' => Str::uuid(30),
                'lab_id' => $lab->id,
                'location' => $desk['location'],
                // 'wall' => $desk['wall'],
                'condition' => $desk['condition'],
                'serial_code' => 'DESK-' . strtoupper(Str::random(8)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
