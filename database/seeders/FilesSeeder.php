<?php

namespace Database\Seeders;

use App\Models\Files;
use App\Models\Folders;
use App\Models\User;
use Illuminate\Database\Seeder;

class FilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Skipping FilesSeeder.');
            return;
        }

        $adminUser = $users->first();

        // Create global repository root folder
        $rootFolder = Folders::create([
            'name' => 'Repository',
            'parent_id' => null,
            'full_path' => '/',
            'is_root' => false,
            'creator_id' => null,
        ]);

        $modulesFolder = Folders::create([
            'name' => 'Modules',
            'parent_id' => $rootFolder->id,
            'full_path' => '/Modules',
            'is_root' => false,
            'creator_id' => null
        ]);

        // Create folders under root
        $folders = [
            ['name' => 'Documents', 'full_path' => '/Documents'],
            ['name' => 'Images', 'full_path' => '/Images'],
            ['name' => 'Videos', 'full_path' => '/Videos'],
            ['name' => 'Reports', 'full_path' => '/Reports'],
            ['name' => 'Manuals', 'full_path' => '/Manuals'],
        ];

        $createdFolders = [];
        foreach ($folders as $folder) {
            $createdFolders[$folder['name']] = Folders::create([
                'name' => $folder['name'],
                'full_path' => $folder['full_path'],
                'parent_id' => $rootFolder->id,
                'is_root' => false,
                'creator_id' => $adminUser->id,
            ]);
        }

        // Create subfolders
        if (isset($createdFolders['Documents'])) {
            Folders::create([
                'name' => 'Lab Procedures',
                'full_path' => '/Documents/Lab Procedures',
                'parent_id' => $createdFolders['Documents']->id,
                'is_root' => false,
                'creator_id' => $adminUser->id,
            ]);

            Folders::create([
                'name' => 'Equipment Specs',
                'full_path' => '/Documents/Equipment Specs',
                'parent_id' => $createdFolders['Documents']->id,
                'is_root' => false,
                'creator_id' => $adminUser->id,
            ]);
        }

        if (isset($createdFolders['Reports'])) {
            Folders::create([
                'name' => 'Monthly',
                'full_path' => '/Reports/Monthly',
                'parent_id' => $createdFolders['Reports']->id,
                'is_root' => false,
                'creator_id' => $adminUser->id,
            ]);

            Folders::create([
                'name' => 'Annual',
                'full_path' => '/Reports/Annual',
                'parent_id' => $createdFolders['Reports']->id,
                'is_root' => false,
                'creator_id' => $adminUser->id,
            ]);
        }
    }
}
