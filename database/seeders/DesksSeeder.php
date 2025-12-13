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
        $labPemrograman = $labs->where('name', 'Lab Pemrograman')->first();
        $labJaringan = $labs->where('name', 'Lab Jaringan Komputer')->first();
        $labStudio = $labs->where('name', 'Lab Studio')->first();
        $labSI = $labs->where('name', 'Lab Sistem Informasi')->first();
        $labSC = $labs->where('name', 'Lab Sistem Cerdas')->first();
        $labMM = $labs->where('name', 'Lab Multimedia')->first();
        $labVR = $labs->where('name', 'Lab Virtual Reality')->first();
        $labMobdeb = $labs->where('name', 'Lab Mobile Development')->first();

        // if (!$labPemrograman || !$labJaringan) {
        //     $this->command->error('Pastikan LabSeeder sudah dijalankan terlebih dahulu!');
        //     return;
        // }

        // Lab Pemrograman Dasar
        $desksPemrograman = [
            ['location' => 'A2'],

            ['location' => 'B1'],
            ['location' => 'B2'],
            ['location' => 'B3'],
            ['location' => 'B4'],
            ['location' => 'B6'],
            ['location' => 'B7'],
            ['location' => 'B8'],

            ['location' => 'C1'],
            ['location' => 'C2'],
            ['location' => 'C3'],
            ['location' => 'C4'],
            ['location' => 'C6'],
            ['location' => 'C7'],
            ['location' => 'C8'],

            ['location' => 'D1'],
            ['location' => 'D2'],
            ['location' => 'D3'],
            ['location' => 'D4'],
            ['location' => 'D6'],
            ['location' => 'D7'],
            ['location' => 'D8'],

            ['location' => 'E2'],
            ['location' => 'E3'],
            ['location' => 'E4'],
            ['location' => 'E6'],
            ['location' => 'E7'],

            ['location' => 'F1'],
            ['location' => 'F2'],
            ['location' => 'F3'],
            ['location' => 'F4'],
            ['location' => 'F6'],
            ['location' => 'F7'],
            ['location' => 'F8'],

            ['location' => 'G1'],
            ['location' => 'G2'],
            ['location' => 'G3'],
            ['location' => 'G4'],
            ['location' => 'G6'],
            ['location' => 'G7'],
            ['location' => 'G8'],

            ['location' => 'H1'],
            ['location' => 'H2'],
            ['location' => 'H3'],
            ['location' => 'H4'],

        ];

        // Lab Jaringan Komputer
        $desksJaringan = [
            ['location' => 'A2'],

            ['location' => 'B2'],
            ['location' => 'B3'],
            ['location' => 'B4'],
            ['location' => 'B5'],
            ['location' => 'B6'],
            ['location' => 'B7'],
            ['location' => 'B8'],

            ['location' => 'C2'],
            ['location' => 'C3'],
            ['location' => 'C4'],
            ['location' => 'C5'],
            ['location' => 'C6'],
            ['location' => 'C7'],
            ['location' => 'C8'],

            ['location' => 'D2'],
            ['location' => 'D3'],
            ['location' => 'D4'],
            ['location' => 'D5'],
            ['location' => 'D6'],
            ['location' => 'D7'],
            ['location' => 'D8'],

        ];

        // Lab Studio
        $desksStudio = [
            ['location' => 'A2'],

            ['location' => 'B1'],
            ['location' => 'B2'],
            ['location' => 'B3'],
            ['location' => 'B5'],
            ['location' => 'B6'],
            ['location' => 'B7'],


            ['location' => 'D1'],
            ['location' => 'D2'],
            ['location' => 'D3'],
            ['location' => 'D5'],
            ['location' => 'D6'],

            ['location' => 'E1'],
            ['location' => 'E2'],
            ['location' => 'E3'],
            ['location' => 'E5'],
            ['location' => 'E6'],


            ['location' => 'G1'],
            ['location' => 'G2'],
            ['location' => 'G3'],
            ['location' => 'G5'],
            ['location' => 'G6'],
            ['location' => 'G7'],

            ['location' => 'H1'],
            ['location' => 'H2'],
            ['location' => 'H3'],
            ['location' => 'H5'],
            ['location' => 'H6'],
            ['location' => 'H7'],


            ['location' => 'J1'],
            ['location' => 'J2'],
            ['location' => 'J3'],
            ['location' => 'J5'],
            ['location' => 'J6'],
            ['location' => 'J7'],

            ['location' => 'K1'],
            ['location' => 'K2'],
            ['location' => 'K3'],
            ['location' => 'K5'],
            ['location' => 'K6'],
            ['location' => 'K7'],

        ];

        $desksSI = [

            ['location' => 'A6'],

            ['location' => 'B1'],
            ['location' => 'B2'],
            ['location' => 'B3'],
            ['location' => 'B4'],
            ['location' => 'B5'],
            ['location' => 'B6'],
            ['location' => 'B7'],
            ['location' => 'B9'],
            ['location' => 'B10'],
            ['location' => 'B11'],
            ['location' => 'B12'],
            ['location' => 'B13'],
            ['location' => 'B14'],
            ['location' => 'B15'],

            ['location' => 'C1'],
            ['location' => 'C2'],
            ['location' => 'C3'],
            ['location' => 'C4'],
            ['location' => 'C5'],
            ['location' => 'C6'],
            ['location' => 'C7'],
            ['location' => 'C9'],
            ['location' => 'C10'],
            ['location' => 'C11'],
            ['location' => 'C12'],
            ['location' => 'C13'],
            ['location' => 'C14'],
            ['location' => 'C15'],

            ['location' => 'D1'],
            ['location' => 'D2'],
            ['location' => 'D3'],
            ['location' => 'D4'],
            ['location' => 'D5'],
            ['location' => 'D6'],
            ['location' => 'D7'],
            ['location' => 'D9'],
            ['location' => 'D10'],
            ['location' => 'D11'],
            ['location' => 'D12'],
            ['location' => 'D13'],
            ['location' => 'D14'],
            ['location' => 'D15'],

            ['location' => 'E1'],
            ['location' => 'E2'],
            ['location' => 'E3'],
            ['location' => 'E4'],
            ['location' => 'E5'],
            ['location' => 'E6'],
            ['location' => 'E7'],
            ['location' => 'E9'],
            ['location' => 'E10'],
            ['location' => 'E11'],
            ['location' => 'E12'],
            ['location' => 'E13'],
            ['location' => 'E14'],
            ['location' => 'E15'],

        ];

        $desksSC = [

            ['location' => 'A3'],

            ['location' => 'B1'],
            ['location' => 'B2'],
            ['location' => 'B3'],
            ['location' => 'B4'],
            ['location' => 'B5'],
            ['location' => 'B6'],

            ['location' => 'C1'],
            ['location' => 'C2'],
            ['location' => 'C3'],
            ['location' => 'C4'],
            ['location' => 'C5'],
            ['location' => 'C6'],

            ['location' => 'D1'],
            ['location' => 'D2'],
            ['location' => 'D3'],
            ['location' => 'D4'],
            ['location' => 'D5'],
            ['location' => 'D6'],

            ['location' => 'E1'],
            ['location' => 'E2'],
            ['location' => 'E3'],
            ['location' => 'E4'],
            ['location' => 'E5'],
            ['location' => 'E6'],

            ['location' => 'F1'],
            ['location' => 'F2'],
            ['location' => 'F3'],
            ['location' => 'F4'],
            ['location' => 'F5'],
            ['location' => 'F6'],

            ['location' => 'G1'],
            ['location' => 'G2'],
            ['location' => 'G3'],
            ['location' => 'G4'],
            ['location' => 'G5'],
            ['location' => 'G6'],

        ];

        $desksMM = [

            ['location' => 'A1'],
            ['location' => 'A2'],
            ['location' => 'A6'],

            ['location' => 'B1'],
            ['location' => 'B2'],
            ['location' => 'B3'],
            ['location' => 'B4'],
            ['location' => 'B6'],
            ['location' => 'B7'],
            ['location' => 'B8'],

            ['location' => 'C1'],
            ['location' => 'C2'],
            ['location' => 'C3'],
            ['location' => 'C4'],
            ['location' => 'C6'],
            ['location' => 'C7'],
            ['location' => 'C8'],

            ['location' => 'D1'],
            ['location' => 'D2'],
            ['location' => 'D3'],
            ['location' => 'D4'],
            ['location' => 'D6'],
            ['location' => 'D7'],
            ['location' => 'D8'],

        ];

        $desksVR = [

            ['location' => 'A1'],
            ['location' => 'A2'],
            ['location' => 'A8'],

            ['location' => 'B6'],
            ['location' => 'B7'],
            ['location' => 'B8'],

            ['location' => 'C6'],
            ['location' => 'C7'],
            ['location' => 'C8'],

            ['location' => 'D1'],

            ['location' => 'E1'],
            ['location' => 'E8'],

            ['location' => 'F1'],
            ['location' => 'F8'],

            ['location' => 'G1'],
            ['location' => 'G8'],

        ];

        $desksMobdev = [

            ['location' => 'A5'],

            ['location' => 'B1'],
            ['location' => 'B2'],
            ['location' => 'B3'],

            ['location' => 'B5'],
            ['location' => 'B6'],
            ['location' => 'B7'],
            ['location' => 'B8'],

            ['location' => 'C1'],
            ['location' => 'C2'],
            ['location' => 'C3'],

            ['location' => 'C5'],
            ['location' => 'C6'],
            ['location' => 'C7'],
            ['location' => 'C8'],

            ['location' => 'D1'],
            ['location' => 'D2'],
            ['location' => 'D3'],

            ['location' => 'D5'],
            ['location' => 'D6'],
            ['location' => 'D7'],
            ['location' => 'D8'],
            
            ['location' => 'E2'],
            ['location' => 'E3'],

            ['location' => 'E5'],
            ['location' => 'E6'],
            ['location' => 'E7'],
            
            ['location' => 'F1'],
            ['location' => 'F2'],
            ['location' => 'F3'],

            ['location' => 'F5'],
            ['location' => 'F6'],
            ['location' => 'F7'],
            
            ['location' => 'G1'],
            ['location' => 'G2'],
            ['location' => 'G3'],

            ['location' => 'G5'],
            ['location' => 'G6'],
            ['location' => 'G7'],
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

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
