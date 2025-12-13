<?php

namespace Database\Seeders;

use App\Models\Period;
use Illuminate\Database\Seeder;

class PeriodSeeder extends Seeder
{
    public function run(): void
    {
        $periods = [
            ['academic_year' => '2022/2023', 'semester' => 'GASAL', 'active' => false],
            ['academic_year' => '2022/2023', 'semester' => 'GENAP', 'active' => false],
            ['academic_year' => '2023/2024', 'semester' => 'GASAL', 'active' => false],
            ['academic_year' => '2023/2024', 'semester' => 'GENAP', 'active' => false],
            ['academic_year' => '2024/2025', 'semester' => 'GASAL', 'active' => true],
            ['academic_year' => '2024/2025', 'semester' => 'GENAP', 'active' => false],
            ['academic_year' => '2025/2026', 'semester' => 'GASAL', 'active' => false],
        ];

        foreach ($periods as $period) {
            Period::firstOrCreate($period);
        }
    }
}