<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permission sudah di-seed di UnitSeeder
        // Seeder ini bisa digunakan untuk menambahkan permission tambahan jika diperlukan
        $this->command->info('Permissions are seeded in UnitSeeder. This seeder is for additional permissions only.');
    }
}
