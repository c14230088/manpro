<?php

namespace Database\Seeders;

use App\Models\Labs;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LabsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labs = [
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Pemrograman',
                'location' => 'P2.06',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Mobile Development',
                'location' => 'P2.07A',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Studio',
                'location' => 'P2.07B',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Sistem Informasi',
                'location' => 'P2.08',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Jaringan Komputer',
                'location' => 'P2.07',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Sistem Cerdas',
                'location' => 'P2.07',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Multimedia',
                'location' => 'P2.07B',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Virtual Reality',
                'location' => 'P2.07A',
                'capacity' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($labs as $lab) {
            Labs::create($lab);
        }
    }
}
