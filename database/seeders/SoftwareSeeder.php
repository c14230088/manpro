<?php

namespace Database\Seeders;

use App\Models\Labs;
use App\Models\Software;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SoftwareSeeder extends Seeder
{
    public function run(): void
    {
        $softwares = [
            ['name' => 'VISUAL STUDIO CODE', 'version' => '1.85.0', 'description' => 'Code editor'],
            ['name' => 'INTELLIJ IDEA', 'version' => '2023.3', 'description' => 'Java IDE'],
            ['name' => 'PYCHARM', 'version' => '2023.3', 'description' => 'Python IDE'],
            ['name' => 'ANDROID STUDIO', 'version' => '2023.1', 'description' => 'Android development'],
            ['name' => 'UNITY', 'version' => '2022.3', 'description' => 'Game engine'],
            ['name' => 'UNREAL ENGINE', 'version' => '5.3', 'description' => 'Game engine'],
            ['name' => 'BLENDER', 'version' => '4.0', 'description' => '3D modeling'],
            ['name' => 'ADOBE PHOTOSHOP', 'version' => '2024', 'description' => 'Image editing'],
            ['name' => 'ADOBE PREMIERE', 'version' => '2024', 'description' => 'Video editing'],
            ['name' => 'MATLAB', 'version' => 'R2023b', 'description' => 'Mathematical computing'],
            ['name' => 'MYSQL WORKBENCH', 'version' => '8.0', 'description' => 'Database management'],
            ['name' => 'POSTGRESQL', 'version' => '16.0', 'description' => 'Database system'],
            ['name' => 'DOCKER', 'version' => '24.0', 'description' => 'Containerization'],
            ['name' => 'GIT', 'version' => '2.43', 'description' => 'Version control'],
            ['name' => 'POSTMAN', 'version' => '10.20', 'description' => 'API testing'],
        ];

        foreach ($softwares as $software) {
            Software::firstOrCreate(['name' => $software['name']], $software);
        }

        // Attach software to labs
        $labs = Labs::all();
        $allSoftware = Software::all();
        
        foreach ($labs as $lab) {
            $softwareCount = rand(5, 10);
            $selectedSoftware = $allSoftware->random($softwareCount);
            
            foreach ($selectedSoftware as $software) {
                DB::table('lab_softwares')->insertOrIgnore([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'lab_id' => $lab->id,
                    'software_id' => $software->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
