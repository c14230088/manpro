<?php

namespace Database\Seeders;

use App\Models\Files;
use App\Models\Folders;
use App\Models\Matkul;
use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping ModuleSeeder.');
            return;
        }

        $adminUser = $users->first();

        // Get Modules folder
        $modulesFolder = Folders::where('name', 'Modules')
            ->whereHas('parent', fn($q) => $q->whereNull('parent_id'))
            ->first();

        if (!$modulesFolder) {
            $this->command->warn('Modules folder not found. Skipping ModuleSeeder.');
            return;
        }

        // Create sample module files
        $moduleFiles = [
            ['name' => 'Introduction_to_Programming.pdf', 'size' => 1024000],
            ['name' => 'Data_Structures_Basics.pdf', 'size' => 2048000],
            ['name' => 'Algorithm_Analysis.pdf', 'size' => 1536000],
            ['name' => 'Database_Design.pdf', 'size' => 2560000],
            ['name' => 'Web_Development_Fundamentals.pdf', 'size' => 3072000],
            ['name' => 'Object_Oriented_Programming.pdf', 'size' => 1792000],
        ];

        $createdFiles = [];
        foreach ($moduleFiles as $fileData) {
            $storedName = Str::uuid() . '.pdf';
            
            $file = Files::create([
                'folder_id' => $modulesFolder->id,
                'original_name' => $fileData['name'],
                'stored_name' => $storedName,
                'mime_type' => 'application/pdf',
                'size' => $fileData['size'],
                'creator_id' => $adminUser->id,
            ]);
            
            $createdFiles[] = $file;
        }

        // Get some matkuls
        $matkuls = Matkul::take(3)->get();

        if ($matkuls->isEmpty()) {
            $this->command->warn('No matkuls found. Skipping module creation.');
            return;
        }

        // Create modules for each matkul
        foreach ($matkuls as $index => $matkul) {
            $fileCount = min(2, count($createdFiles) - ($index * 2));
            
            for ($i = 0; $i < $fileCount; $i++) {
                $fileIndex = ($index * 2) + $i;
                if (isset($createdFiles[$fileIndex])) {
                    Module::create([
                        'file_id' => $createdFiles[$fileIndex]->id,
                        'matkul_id' => $matkul->id,
                        'author_id' => $adminUser->id,
                        'workload_hours' => rand(2, 8),
                        'last_edited_at' => now(),
                        'last_edited_by' => $adminUser->id,
                        'active' => true,
                    ]);
                }
            }
        }
    }
}
