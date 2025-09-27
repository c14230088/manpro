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
            ['location' => 'A1', 'wall' => true, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => true, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => true, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],
            ['location' => 'A8', 'wall' => true, 'condition' => 1],

            ['location' => 'B1', 'wall' => false, 'condition' => 1],
            ['location' => 'B2', 'wall' => false, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
            ['location' => 'B4', 'wall' => false, 'condition' => 1],
            ['location' => 'B5', 'wall' => true, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => false, 'condition' => 1],
            ['location' => 'B8', 'wall' => false, 'condition' => 1],

            ['location' => 'C1', 'wall' => false, 'condition' => 1],
            ['location' => 'C2', 'wall' => false, 'condition' => 1],
            ['location' => 'C3', 'wall' => false, 'condition' => 1],
            ['location' => 'C4', 'wall' => false, 'condition' => 1],
            ['location' => 'C5', 'wall' => true, 'condition' => 1],
            ['location' => 'C6', 'wall' => false, 'condition' => 1],
            ['location' => 'C7', 'wall' => false, 'condition' => 1],
            ['location' => 'C8', 'wall' => false, 'condition' => 1],

            ['location' => 'D1', 'wall' => false, 'condition' => 1],
            ['location' => 'D2', 'wall' => false, 'condition' => 1],
            ['location' => 'D3', 'wall' => false, 'condition' => 1],
            ['location' => 'D4', 'wall' => false, 'condition' => 1],
            ['location' => 'D5', 'wall' => true, 'condition' => 1],
            ['location' => 'D6', 'wall' => false, 'condition' => 1],
            ['location' => 'D7', 'wall' => false, 'condition' => 1],
            ['location' => 'D8', 'wall' => false, 'condition' => 1],

            ['location' => 'E1', 'wall' => true, 'condition' => 1],
            ['location' => 'E2', 'wall' => false, 'condition' => 1],
            ['location' => 'E3', 'wall' => false, 'condition' => 1],
            ['location' => 'E4', 'wall' => false, 'condition' => 1],
            ['location' => 'E5', 'wall' => true, 'condition' => 1],
            ['location' => 'E6', 'wall' => false, 'condition' => 1],
            ['location' => 'E7', 'wall' => false, 'condition' => 1],
            ['location' => 'E8', 'wall' => true, 'condition' => 1],

            ['location' => 'F1', 'wall' => false, 'condition' => 1],
            ['location' => 'F2', 'wall' => false, 'condition' => 1],
            ['location' => 'F3', 'wall' => false, 'condition' => 1],
            ['location' => 'F4', 'wall' => false, 'condition' => 1],
            ['location' => 'F5', 'wall' => true, 'condition' => 1],
            ['location' => 'F6', 'wall' => false, 'condition' => 1],
            ['location' => 'F7', 'wall' => false, 'condition' => 1],
            ['location' => 'F8', 'wall' => false, 'condition' => 1],

            ['location' => 'G1', 'wall' => false, 'condition' => 1],
            ['location' => 'G2', 'wall' => false, 'condition' => 1],
            ['location' => 'G3', 'wall' => false, 'condition' => 1],
            ['location' => 'G4', 'wall' => false, 'condition' => 1],
            ['location' => 'G5', 'wall' => true, 'condition' => 1],
            ['location' => 'G6', 'wall' => false, 'condition' => 1],
            ['location' => 'G7', 'wall' => false, 'condition' => 1],
            ['location' => 'G8', 'wall' => false, 'condition' => 1],

            ['location' => 'H1', 'wall' => false, 'condition' => 1],
            ['location' => 'H2', 'wall' => false, 'condition' => 1],
            ['location' => 'H3', 'wall' => false, 'condition' => 1],
            ['location' => 'H4', 'wall' => false, 'condition' => 1],
            ['location' => 'H5', 'wall' => true, 'condition' => 1],
            ['location' => 'H6', 'wall' => true, 'condition' => 1],
            ['location' => 'H7', 'wall' => true, 'condition' => 1],
            ['location' => 'H8', 'wall' => true, 'condition' => 1],

        ];

        // Lab Jaringan Komputer
        $desksJaringan = [
            ['location' => 'A1', 'wall' => true, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => true, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => true, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],
            ['location' => 'A8', 'wall' => true, 'condition' => 1],

            ['location' => 'B1', 'wall' => true, 'condition' => 1],
            ['location' => 'B2', 'wall' => false, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
            ['location' => 'B4', 'wall' => false, 'condition' => 1],
            ['location' => 'B5', 'wall' => false, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => false, 'condition' => 1],
            ['location' => 'B8', 'wall' => false, 'condition' => 1],

            ['location' => 'C1', 'wall' => true, 'condition' => 1],
            ['location' => 'C2', 'wall' => false, 'condition' => 1],
            ['location' => 'C3', 'wall' => false, 'condition' => 1],
            ['location' => 'C4', 'wall' => false, 'condition' => 1],
            ['location' => 'C5', 'wall' => false, 'condition' => 1],
            ['location' => 'C6', 'wall' => false, 'condition' => 1],
            ['location' => 'C7', 'wall' => false, 'condition' => 1],
            ['location' => 'C8', 'wall' => false, 'condition' => 1],

            ['location' => 'D1', 'wall' => true, 'condition' => 1],
            ['location' => 'D2', 'wall' => false, 'condition' => 1],
            ['location' => 'D3', 'wall' => false, 'condition' => 1],
            ['location' => 'D4', 'wall' => false, 'condition' => 1],
            ['location' => 'D5', 'wall' => false, 'condition' => 1],
            ['location' => 'D6', 'wall' => false, 'condition' => 1],
            ['location' => 'D7', 'wall' => false, 'condition' => 1],
            ['location' => 'D8', 'wall' => false, 'condition' => 1],

        ];

        // Lab Studio
        $desksStudio = [
            ['location' => 'A1', 'wall' => true, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => true, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => true, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],

            ['location' => 'B1', 'wall' => false, 'condition' => 1],
            ['location' => 'B2', 'wall' => false, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
            ['location' => 'B4', 'wall' => true, 'condition' => 1],
            ['location' => 'B5', 'wall' => false, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => false, 'condition' => 1],

            ['location' => 'C1', 'wall' => true, 'condition' => 1],
            ['location' => 'C2', 'wall' => true, 'condition' => 1],
            ['location' => 'C3', 'wall' => true, 'condition' => 1],
            ['location' => 'C4', 'wall' => true, 'condition' => 1],
            ['location' => 'C5', 'wall' => true, 'condition' => 1],
            ['location' => 'C6', 'wall' => true, 'condition' => 1],
            ['location' => 'C7', 'wall' => true, 'condition' => 1],

            ['location' => 'D1', 'wall' => false, 'condition' => 1],
            ['location' => 'D2', 'wall' => false, 'condition' => 1],
            ['location' => 'D3', 'wall' => false, 'condition' => 1],
            ['location' => 'D4', 'wall' => true, 'condition' => 1],
            ['location' => 'D5', 'wall' => false, 'condition' => 1],
            ['location' => 'D6', 'wall' => false, 'condition' => 1],
            ['location' => 'D7', 'wall' => true, 'condition' => 1],

            ['location' => 'E1', 'wall' => false, 'condition' => 1],
            ['location' => 'E2', 'wall' => false, 'condition' => 1],
            ['location' => 'E3', 'wall' => false, 'condition' => 1],
            ['location' => 'E4', 'wall' => true, 'condition' => 1],
            ['location' => 'E5', 'wall' => false, 'condition' => 1],
            ['location' => 'E6', 'wall' => false, 'condition' => 1],
            ['location' => 'E7', 'wall' => true, 'condition' => 1],

            ['location' => 'F1', 'wall' => true, 'condition' => 1],
            ['location' => 'F2', 'wall' => true, 'condition' => 1],
            ['location' => 'F3', 'wall' => true, 'condition' => 1],
            ['location' => 'F4', 'wall' => true, 'condition' => 1],
            ['location' => 'F5', 'wall' => true, 'condition' => 1],
            ['location' => 'F6', 'wall' => true, 'condition' => 1],
            ['location' => 'F7', 'wall' => true, 'condition' => 1],

            ['location' => 'G1', 'wall' => false, 'condition' => 1],
            ['location' => 'G2', 'wall' => false, 'condition' => 1],
            ['location' => 'G3', 'wall' => false, 'condition' => 1],
            ['location' => 'G4', 'wall' => true, 'condition' => 1],
            ['location' => 'G5', 'wall' => false, 'condition' => 1],
            ['location' => 'G6', 'wall' => false, 'condition' => 1],
            ['location' => 'G7', 'wall' => false, 'condition' => 1],

            ['location' => 'H1', 'wall' => false, 'condition' => 1],
            ['location' => 'H2', 'wall' => false, 'condition' => 1],
            ['location' => 'H3', 'wall' => false, 'condition' => 1],
            ['location' => 'H4', 'wall' => true, 'condition' => 1],
            ['location' => 'H5', 'wall' => false, 'condition' => 1],
            ['location' => 'H6', 'wall' => false, 'condition' => 1],
            ['location' => 'H7', 'wall' => false, 'condition' => 1],

            ['location' => 'I1', 'wall' => true, 'condition' => 1],
            ['location' => 'I2', 'wall' => true, 'condition' => 1],
            ['location' => 'I3', 'wall' => true, 'condition' => 1],
            ['location' => 'I4', 'wall' => true, 'condition' => 1],
            ['location' => 'I5', 'wall' => true, 'condition' => 1],
            ['location' => 'I6', 'wall' => true, 'condition' => 1],
            ['location' => 'I7', 'wall' => true, 'condition' => 1],

            ['location' => 'J1', 'wall' => false, 'condition' => 1],
            ['location' => 'J2', 'wall' => false, 'condition' => 1],
            ['location' => 'J3', 'wall' => false, 'condition' => 1],
            ['location' => 'J4', 'wall' => true, 'condition' => 1],
            ['location' => 'J5', 'wall' => false, 'condition' => 1],
            ['location' => 'J6', 'wall' => false, 'condition' => 1],
            ['location' => 'J7', 'wall' => false, 'condition' => 1],

            ['location' => 'K1', 'wall' => false, 'condition' => 1],
            ['location' => 'K2', 'wall' => false, 'condition' => 1],
            ['location' => 'K3', 'wall' => false, 'condition' => 1],
            ['location' => 'K4', 'wall' => true, 'condition' => 1],
            ['location' => 'K5', 'wall' => false, 'condition' => 1],
            ['location' => 'K6', 'wall' => false, 'condition' => 1],
            ['location' => 'K7', 'wall' => false, 'condition' => 1],

        ];

        $desksSI = [

            ['location' => 'A1', 'wall' => true, 'condition' => 1],
            ['location' => 'A2', 'wall' => true, 'condition' => 1],
            ['location' => 'A3', 'wall' => true, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => false, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],
            ['location' => 'A8', 'wall' => true, 'condition' => 1],
            ['location' => 'A9', 'wall' => true, 'condition' => 1],
            ['location' => 'A10', 'wall' => true, 'condition' => 1],
            ['location' => 'A11', 'wall' => true, 'condition' => 1],
            ['location' => 'A12', 'wall' => true, 'condition' => 1],
            ['location' => 'A13', 'wall' => true, 'condition' => 1],
            ['location' => 'A14', 'wall' => true, 'condition' => 1],
            ['location' => 'A15', 'wall' => true, 'condition' => 1],

            ['location' => 'B1', 'wall' => false, 'condition' => 1],
            ['location' => 'B2', 'wall' => false, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
            ['location' => 'B4', 'wall' => false, 'condition' => 1],
            ['location' => 'B5', 'wall' => false, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => false, 'condition' => 1],
            ['location' => 'B8', 'wall' => true, 'condition' => 1],
            ['location' => 'B9', 'wall' => false, 'condition' => 1],
            ['location' => 'B10', 'wall' => false, 'condition' => 1],
            ['location' => 'B11', 'wall' => false, 'condition' => 1],
            ['location' => 'B12', 'wall' => false, 'condition' => 1],
            ['location' => 'B13', 'wall' => false, 'condition' => 1],
            ['location' => 'B14', 'wall' => false, 'condition' => 1],
            ['location' => 'B15', 'wall' => false, 'condition' => 1],

            ['location' => 'C1', 'wall' => false, 'condition' => 1],
            ['location' => 'C2', 'wall' => false, 'condition' => 1],
            ['location' => 'C3', 'wall' => false, 'condition' => 1],
            ['location' => 'C4', 'wall' => false, 'condition' => 1],
            ['location' => 'C5', 'wall' => false, 'condition' => 1],
            ['location' => 'C6', 'wall' => false, 'condition' => 1],
            ['location' => 'C7', 'wall' => false, 'condition' => 1],
            ['location' => 'C8', 'wall' => true, 'condition' => 1],
            ['location' => 'C9', 'wall' => false, 'condition' => 1],
            ['location' => 'C10', 'wall' => false, 'condition' => 0],
            ['location' => 'C11', 'wall' => false, 'condition' => 1],
            ['location' => 'C12', 'wall' => false, 'condition' => 1],
            ['location' => 'C13', 'wall' => false, 'condition' => 1],
            ['location' => 'C14', 'wall' => false, 'condition' => 1],
            ['location' => 'C15', 'wall' => false, 'condition' => 1],

            ['location' => 'D1', 'wall' => false, 'condition' => 1],
            ['location' => 'D2', 'wall' => false, 'condition' => 1],
            ['location' => 'D3', 'wall' => false, 'condition' => 1],
            ['location' => 'D4', 'wall' => false, 'condition' => 1],
            ['location' => 'D5', 'wall' => false, 'condition' => 1],
            ['location' => 'D6', 'wall' => false, 'condition' => 1],
            ['location' => 'D7', 'wall' => false, 'condition' => 1],
            ['location' => 'D8', 'wall' => true, 'condition' => 1],
            ['location' => 'D9', 'wall' => false, 'condition' => 1],
            ['location' => 'D10', 'wall' => false, 'condition' => 1],
            ['location' => 'D11', 'wall' => false, 'condition' => 1],
            ['location' => 'D12', 'wall' => false, 'condition' => 1],
            ['location' => 'D13', 'wall' => false, 'condition' => 1],
            ['location' => 'D14', 'wall' => false, 'condition' => 1],
            ['location' => 'D15', 'wall' => false, 'condition' => 1],

            ['location' => 'E1', 'wall' => false, 'condition' => 1],
            ['location' => 'E2', 'wall' => false, 'condition' => 1],
            ['location' => 'E3', 'wall' => false, 'condition' => 1],
            ['location' => 'E4', 'wall' => false, 'condition' => 1],
            ['location' => 'E5', 'wall' => false, 'condition' => 1],
            ['location' => 'E6', 'wall' => false, 'condition' => 1],
            ['location' => 'E7', 'wall' => false, 'condition' => 1],
            ['location' => 'E8', 'wall' => true, 'condition' => 1],
            ['location' => 'E9', 'wall' => false, 'condition' => 1],
            ['location' => 'E10', 'wall' => false, 'condition' => 1],
            ['location' => 'E11', 'wall' => false, 'condition' => 1],
            ['location' => 'E12', 'wall' => false, 'condition' => 1],
            ['location' => 'E13', 'wall' => false, 'condition' => 1],
            ['location' => 'E14', 'wall' => false, 'condition' => 1],
            ['location' => 'E15', 'wall' => false, 'condition' => 1],

        ];

        $desksSC = [

            ['location' => 'A1', 'wall' => true, 'condition' => 1],
            ['location' => 'A2', 'wall' => true, 'condition' => 1],
            ['location' => 'A3', 'wall' => false, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => true, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],

            ['location' => 'B1', 'wall' => false, 'condition' => 1],
            ['location' => 'B2', 'wall' => false, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
            ['location' => 'B4', 'wall' => false, 'condition' => 1],
            ['location' => 'B5', 'wall' => false, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => true, 'condition' => 1],

            ['location' => 'C1', 'wall' => false, 'condition' => 1],
            ['location' => 'C2', 'wall' => false, 'condition' => 1],
            ['location' => 'C3', 'wall' => false, 'condition' => 1],
            ['location' => 'C4', 'wall' => false, 'condition' => 1],
            ['location' => 'C5', 'wall' => false, 'condition' => 1],
            ['location' => 'C6', 'wall' => false, 'condition' => 1],
            ['location' => 'C7', 'wall' => true, 'condition' => 1],

            ['location' => 'D1', 'wall' => false, 'condition' => 1],
            ['location' => 'D2', 'wall' => false, 'condition' => 1],
            ['location' => 'D3', 'wall' => false, 'condition' => 1],
            ['location' => 'D4', 'wall' => false, 'condition' => 1],
            ['location' => 'D5', 'wall' => false, 'condition' => 1],
            ['location' => 'D6', 'wall' => false, 'condition' => 1],
            ['location' => 'D7', 'wall' => true, 'condition' => 1],

            ['location' => 'E1', 'wall' => false, 'condition' => 1],
            ['location' => 'E2', 'wall' => false, 'condition' => 1],
            ['location' => 'E3', 'wall' => false, 'condition' => 1],
            ['location' => 'E4', 'wall' => false, 'condition' => 1],
            ['location' => 'E5', 'wall' => false, 'condition' => 1],
            ['location' => 'E6', 'wall' => false, 'condition' => 1],
            ['location' => 'E7', 'wall' => true, 'condition' => 1],

            ['location' => 'F1', 'wall' => false, 'condition' => 1],
            ['location' => 'F2', 'wall' => false, 'condition' => 1],
            ['location' => 'F3', 'wall' => false, 'condition' => 1],
            ['location' => 'F4', 'wall' => false, 'condition' => 1],
            ['location' => 'F5', 'wall' => false, 'condition' => 1],
            ['location' => 'F6', 'wall' => false, 'condition' => 1],
            ['location' => 'F7', 'wall' => true, 'condition' => 1],

            ['location' => 'G1', 'wall' => false, 'condition' => 1],
            ['location' => 'G2', 'wall' => false, 'condition' => 1],
            ['location' => 'G3', 'wall' => false, 'condition' => 1],
            ['location' => 'G4', 'wall' => false, 'condition' => 1],
            ['location' => 'G5', 'wall' => false, 'condition' => 1],
            ['location' => 'G6', 'wall' => false, 'condition' => 1],
            ['location' => 'G7', 'wall' => true, 'condition' => 1],

        ];

        $desksMM = [

            ['location' => 'A1', 'wall' => false, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => true, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => false, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],
            ['location' => 'A8', 'wall' => true, 'condition' => 1],

            ['location' => 'B1', 'wall' => false, 'condition' => 1],
            ['location' => 'B2', 'wall' => false, 'condition' => 1],
            ['location' => 'B3', 'wall' => false, 'condition' => 1],
            ['location' => 'B4', 'wall' => false, 'condition' => 1],
            ['location' => 'B5', 'wall' => true, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => false, 'condition' => 1],
            ['location' => 'B8', 'wall' => false, 'condition' => 1],

            ['location' => 'C1', 'wall' => false, 'condition' => 1],
            ['location' => 'C2', 'wall' => false, 'condition' => 1],
            ['location' => 'C3', 'wall' => false, 'condition' => 1],
            ['location' => 'C4', 'wall' => false, 'condition' => 1],
            ['location' => 'C5', 'wall' => true, 'condition' => 1],
            ['location' => 'C6', 'wall' => false, 'condition' => 1],
            ['location' => 'C7', 'wall' => false, 'condition' => 1],
            ['location' => 'C8', 'wall' => false, 'condition' => 1],

            ['location' => 'D1', 'wall' => false, 'condition' => 1],
            ['location' => 'D2', 'wall' => false, 'condition' => 1],
            ['location' => 'D3', 'wall' => false, 'condition' => 1],
            ['location' => 'D4', 'wall' => false, 'condition' => 1],
            ['location' => 'D5', 'wall' => true, 'condition' => 1],
            ['location' => 'D6', 'wall' => false, 'condition' => 1],
            ['location' => 'D7', 'wall' => false, 'condition' => 1],
            ['location' => 'D8', 'wall' => false, 'condition' => 1],

        ];

        $desksVR = [

            ['location' => 'A1', 'wall' => false, 'condition' => 1],
            ['location' => 'A2', 'wall' => false, 'condition' => 1],
            ['location' => 'A3', 'wall' => true, 'condition' => 1],
            ['location' => 'A4', 'wall' => true, 'condition' => 1],
            ['location' => 'A5', 'wall' => true, 'condition' => 1],
            ['location' => 'A6', 'wall' => true, 'condition' => 1],
            ['location' => 'A7', 'wall' => true, 'condition' => 1],
            ['location' => 'A8', 'wall' => false, 'condition' => 1],

            ['location' => 'B1', 'wall' => true, 'condition' => 1],
            ['location' => 'B2', 'wall' => true, 'condition' => 1],
            ['location' => 'B3', 'wall' => true, 'condition' => 1],
            ['location' => 'B4', 'wall' => true, 'condition' => 1],
            ['location' => 'B5', 'wall' => true, 'condition' => 1],
            ['location' => 'B6', 'wall' => false, 'condition' => 1],
            ['location' => 'B7', 'wall' => false, 'condition' => 1],
            ['location' => 'B8', 'wall' => false, 'condition' => 1],

            ['location' => 'C1', 'wall' => true, 'condition' => 1],
            ['location' => 'C2', 'wall' => true, 'condition' => 1],
            ['location' => 'C3', 'wall' => true, 'condition' => 1],
            ['location' => 'C4', 'wall' => true, 'condition' => 1],
            ['location' => 'C5', 'wall' => true, 'condition' => 1],
            ['location' => 'C6', 'wall' => false, 'condition' => 1],
            ['location' => 'C7', 'wall' => false, 'condition' => 1],
            ['location' => 'C8', 'wall' => false, 'condition' => 1],

            ['location' => 'D1', 'wall' => false, 'condition' => 1],
            ['location' => 'D2', 'wall' => true, 'condition' => 1],
            ['location' => 'D3', 'wall' => true, 'condition' => 1],
            ['location' => 'D4', 'wall' => true, 'condition' => 1],
            ['location' => 'D5', 'wall' => true, 'condition' => 1],
            ['location' => 'D6', 'wall' => true, 'condition' => 1],
            ['location' => 'D7', 'wall' => true, 'condition' => 1],
            ['location' => 'D8', 'wall' => true, 'condition' => 1],

            ['location' => 'E1', 'wall' => false, 'condition' => 1],
            ['location' => 'E2', 'wall' => true, 'condition' => 1],
            ['location' => 'E3', 'wall' => true, 'condition' => 1],
            ['location' => 'E4', 'wall' => true, 'condition' => 1],
            ['location' => 'E5', 'wall' => true, 'condition' => 1],
            ['location' => 'E6', 'wall' => true, 'condition' => 1],
            ['location' => 'E7', 'wall' => true, 'condition' => 1],
            ['location' => 'E8', 'wall' => false, 'condition' => 1],

            ['location' => 'F1', 'wall' => false, 'condition' => 1],
            ['location' => 'F2', 'wall' => true, 'condition' => 1],
            ['location' => 'F3', 'wall' => true, 'condition' => 1],
            ['location' => 'F4', 'wall' => true, 'condition' => 1],
            ['location' => 'F5', 'wall' => true, 'condition' => 1],
            ['location' => 'F6', 'wall' => true, 'condition' => 1],
            ['location' => 'F7', 'wall' => true, 'condition' => 1],
            ['location' => 'F8', 'wall' => false, 'condition' => 1],

            ['location' => 'G1', 'wall' => false, 'condition' => 1],
            ['location' => 'G2', 'wall' => true, 'condition' => 1],
            ['location' => 'G3', 'wall' => true, 'condition' => 1],
            ['location' => 'G4', 'wall' => true, 'condition' => 1],
            ['location' => 'G5', 'wall' => true, 'condition' => 1],
            ['location' => 'G6', 'wall' => true, 'condition' => 1],
            ['location' => 'G7', 'wall' => true, 'condition' => 1],
            ['location' => 'G8', 'wall' => false, 'condition' => 1],

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
                'wall' => $desk['wall'],
                'condition' => $desk['condition'],
                'serial_code' => 'DESK-' . strtoupper(Str::random(8)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
