<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $units = Unit::all();
        if ($units->isEmpty()) return;

        $ptikUnit = $units->where('name', 'PTIK')->first();
        $uppkUnit = $units->where('name', 'UPPK')->first();
        $mhswUnit = $units->where('name', 'MAHASISWA')->first();

        // Admin/Staff users
        User::firstOrCreate(['email' => 'admin@petra.ac.id'], [
            'name' => 'Admin System',
            'unit_id' => $ptikUnit?->id ?? $units->first()->id,
        ]);

        // Lecturers
        $lecturers = [
            ['name' => 'Dr. John Doe', 'email' => 'john.doe@petra.ac.id'],
            ['name' => 'Prof. Jane Smith', 'email' => 'jane.smith@petra.ac.id'],
            ['name' => 'Dr. Michael Johnson', 'email' => 'michael.johnson@petra.ac.id'],
        ];

        foreach ($lecturers as $lecturer) {
            User::firstOrCreate(['email' => $lecturer['email']], [
                'name' => $lecturer['name'],
                'unit_id' => $ptikUnit?->id ?? $units->first()->id,
            ]);
        }

        // Students
        $students = [
            ['name' => 'Alice Student', 'email' => 'alice@student.petra.ac.id'],
            ['name' => 'Bob Student', 'email' => 'bob@student.petra.ac.id'],
            ['name' => 'Charlie Student', 'email' => 'charlie@student.petra.ac.id'],
        ];

        foreach ($students as $student) {
            User::firstOrCreate(['email' => $student['email']], [
                'name' => $student['name'],
                'unit_id' => $mhswUnit?->id ?? $units->first()->id,
            ]);
        }
    }
}