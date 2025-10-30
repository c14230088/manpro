<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'id' => Str::uuid(30),
                'name' => 'UPPK',
                'description' => 'Unit Pengelola Prasarana dan Komputer',
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'PTIK',
                'description' => 'Program Teknologi Informasi dan Komputer',
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Prodi Informatika',
                'description' => 'Program Studi Informatika',
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Mahasiswa',
                'description' => 'Mahasiswa Petra',
            ],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
