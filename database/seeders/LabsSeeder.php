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
                'name' => 'Lab Pemrograman Dasar',
                'location' => 'P2.02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Jaringan Komputer',
                'location' => 'P2.07',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(30),
                'name' => 'Lab Multimedia',
                'location' => 'P2.10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        foreach ($labs as $lab) {
            Labs::create($lab);
        }
    }
}
